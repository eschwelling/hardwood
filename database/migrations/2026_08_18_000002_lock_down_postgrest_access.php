<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Closes off Supabase's auto-generated REST API (PostgREST) from this
 * app's tables.
 *
 * Supabase exposes everything in the `public` schema over HTTP and grants
 * its `anon` and `authenticated` roles full DML on it. The anon key is
 * meant to be published in client-side code, so those roles are only ever
 * meant to be constrained by row level security. This app never touches
 * PostgREST — Laravel talks to Postgres directly — so with no RLS in
 * place, every table was readable and writable by anyone holding that
 * public key, including users (password hashes), sessions and
 * password_reset_tokens.
 *
 * Two layers here:
 *   1. RLS on with no policies, which denies those roles by default.
 *   2. The underlying grants revoked outright, plus default privileges
 *      adjusted so tables added later don't get them back.
 *
 * Laravel connects as the table owner (`postgres`, which carries
 * BYPASSRLS), so none of this affects the application's own queries.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE t record;
            BEGIN
                FOR t IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
                LOOP
                    EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', t.tablename);
                END LOOP;
            END $$;
        SQL);

        // Guarded: these roles only exist on Supabase, not on a plain
        // Postgres used for local development.
        DB::unprepared(<<<'SQL'
            DO $$
            BEGIN
                IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'anon')
                   AND EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'authenticated') THEN
                    REVOKE ALL ON ALL TABLES IN SCHEMA public FROM anon, authenticated;
                    REVOKE ALL ON ALL SEQUENCES IN SCHEMA public FROM anon, authenticated;
                    REVOKE ALL ON SCHEMA public FROM anon, authenticated;

                    ALTER DEFAULT PRIVILEGES IN SCHEMA public
                        REVOKE ALL ON TABLES FROM anon, authenticated;
                    ALTER DEFAULT PRIVILEGES IN SCHEMA public
                        REVOKE ALL ON SEQUENCES FROM anon, authenticated;
                END IF;
            END $$;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE t record;
            BEGIN
                FOR t IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
                LOOP
                    EXECUTE format('ALTER TABLE public.%I DISABLE ROW LEVEL SECURITY', t.tablename);
                END LOOP;
            END $$;
        SQL);

        DB::unprepared(<<<'SQL'
            DO $$
            BEGIN
                IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'anon')
                   AND EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'authenticated') THEN
                    GRANT USAGE ON SCHEMA public TO anon, authenticated;
                    GRANT ALL ON ALL TABLES IN SCHEMA public TO anon, authenticated;
                    GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO anon, authenticated;

                    ALTER DEFAULT PRIVILEGES IN SCHEMA public
                        GRANT ALL ON TABLES TO anon, authenticated;
                    ALTER DEFAULT PRIVILEGES IN SCHEMA public
                        GRANT ALL ON SEQUENCES TO anon, authenticated;
                END IF;
            END $$;
        SQL);
    }
};
