-- Runs once, on first container start only (postgres image convention:
-- scripts in /docker-entrypoint-initdb.d/ only execute against an empty data directory).
-- Creates the dedicated database used by phpunit.xml, kept separate from the
-- dev database so running tests never touches dev data.
CREATE DATABASE claude_demo_test;
