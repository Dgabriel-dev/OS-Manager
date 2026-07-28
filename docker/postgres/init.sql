-- PostgreSQL initialization script
-- Creates the database schema if needed

CREATE SCHEMA IF NOT EXISTS os AUTHORIZATION ${POSTGRES_USER};
SET search_path TO os, public;
