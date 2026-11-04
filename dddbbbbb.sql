--
-- PostgreSQL database dump
--

\restrict KUIya2yQ14AVY982dPJvwevgqj9L7PVUrA4779547scGJK7fM9X0K0IvkozDvGa

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2026-11-04 21:24:55

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE IF EXISTS "salle_fetes";
--
-- TOC entry 5429 (class 1262 OID 29751)
-- Name: salle_fetes; Type: DATABASE; Schema: -; Owner: ITAdmin
--

CREATE DATABASE "salle_fetes" WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'French_France.1252';


ALTER DATABASE "salle_fetes" OWNER TO "ITAdmin";

\unrestrict KUIya2yQ14AVY982dPJvwevgqj9L7PVUrA4779547scGJK7fM9X0K0IvkozDvGa
\connect "salle_fetes"
\restrict KUIya2yQ14AVY982dPJvwevgqj9L7PVUrA4779547scGJK7fM9X0K0IvkozDvGa

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA "public";


ALTER SCHEMA "public" OWNER TO "pg_database_owner";

--
-- TOC entry 5430 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA "public"; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA "public" IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = "heap";

--
-- TOC entry 235 (class 1259 OID 29908)
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."audit_logs" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "user_id" bigint,
    "action" character varying(50) NOT NULL,
    "auditable_type" character varying(100) NOT NULL,
    "auditable_id" bigint NOT NULL,
    "old_values" json,
    "new_values" json,
    "ip_address" character varying(45),
    "user_agent" "text",
    "created_at" timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE "public"."audit_logs" OWNER TO "ITAdmin";

--
-- TOC entry 234 (class 1259 OID 29907)
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."audit_logs_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."audit_logs_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5431 (class 0 OID 0)
-- Dependencies: 234
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."audit_logs_id_seq" OWNED BY "public"."audit_logs"."id";


--
-- TOC entry 222 (class 1259 OID 29763)
-- Name: businesses; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."businesses" (
    "id" bigint NOT NULL,
    "name_fr" character varying(150) NOT NULL,
    "name_ar" character varying(150) NOT NULL,
    "slug" character varying(100) NOT NULL,
    "phone" character varying(30) NOT NULL,
    "whatsapp" character varying(30),
    "email" character varying(150),
    "wilaya" character varying(100) DEFAULT 'Alger'::character varying NOT NULL,
    "commune" character varying(120) DEFAULT 'Alger Centre'::character varying NOT NULL,
    "address_line" "text",
    "nif" character varying(30),
    "nis" character varying(30),
    "rc" character varying(30),
    "logo_path" character varying(255),
    "currency" character varying(5) DEFAULT 'DZD'::character varying NOT NULL,
    "timezone" character varying(50) DEFAULT 'Africa/Algiers'::character varying NOT NULL,
    "settings" json,
    "is_active" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."businesses" OWNER TO "ITAdmin";

--
-- TOC entry 221 (class 1259 OID 29762)
-- Name: businesses_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."businesses_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."businesses_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5432 (class 0 OID 0)
-- Dependencies: 221
-- Name: businesses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."businesses_id_seq" OWNED BY "public"."businesses"."id";


--
-- TOC entry 223 (class 1259 OID 29788)
-- Name: cache; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."cache" (
    "key" character varying(255) NOT NULL,
    "value" "text" NOT NULL,
    "expiration" bigint NOT NULL
);


ALTER TABLE "public"."cache" OWNER TO "ITAdmin";

--
-- TOC entry 224 (class 1259 OID 29799)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."cache_locks" (
    "key" character varying(255) NOT NULL,
    "owner" character varying(255) NOT NULL,
    "expiration" bigint NOT NULL
);


ALTER TABLE "public"."cache_locks" OWNER TO "ITAdmin";

--
-- TOC entry 252 (class 1259 OID 30272)
-- Name: cash_registers; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."cash_registers" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "register_date" "date" NOT NULL,
    "opening_balance" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "total_cash_in" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "total_cash_out" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "expected_closing_balance" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "actual_closing_balance" numeric(12,2),
    "status" character varying(20) DEFAULT 'OPEN'::character varying NOT NULL,
    "opened_by" bigint NOT NULL,
    "closed_by" bigint,
    "opened_at" timestamp(0) without time zone NOT NULL,
    "closed_at" timestamp(0) without time zone,
    "notes" "text",
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."cash_registers" OWNER TO "ITAdmin";

--
-- TOC entry 251 (class 1259 OID 30271)
-- Name: cash_registers_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."cash_registers_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."cash_registers_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5433 (class 0 OID 0)
-- Dependencies: 251
-- Name: cash_registers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."cash_registers_id_seq" OWNED BY "public"."cash_registers"."id";


--
-- TOC entry 246 (class 1259 OID 30081)
-- Name: communes; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."communes" (
    "id" bigint NOT NULL,
    "wilaya_id" smallint NOT NULL,
    "name_fr" character varying(120) NOT NULL,
    "name_ar" character varying(120) NOT NULL,
    "postcode" character varying(10)
);


ALTER TABLE "public"."communes" OWNER TO "ITAdmin";

--
-- TOC entry 245 (class 1259 OID 30080)
-- Name: communes_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."communes_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."communes_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5434 (class 0 OID 0)
-- Dependencies: 245
-- Name: communes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."communes_id_seq" OWNED BY "public"."communes"."id";


--
-- TOC entry 248 (class 1259 OID 30098)
-- Name: customers; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."customers" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "customer_code" character varying(30) NOT NULL,
    "full_name" character varying(150) NOT NULL,
    "phone" character varying(30) NOT NULL,
    "second_phone" character varying(30),
    "whatsapp_number" character varying(30),
    "email" character varying(150),
    "wilaya_id" smallint,
    "commune_id" bigint,
    "address_line" "text",
    "bride_name" character varying(120),
    "groom_name" character varying(120),
    "preferred_language" character varying(5) DEFAULT 'fr'::character varying NOT NULL,
    "source" character varying(50) DEFAULT 'WALK_IN'::character varying NOT NULL,
    "internal_notes" "text",
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."customers" OWNER TO "ITAdmin";

--
-- TOC entry 247 (class 1259 OID 30097)
-- Name: customers_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."customers_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."customers_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5435 (class 0 OID 0)
-- Dependencies: 247
-- Name: customers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."customers_id_seq" OWNED BY "public"."customers"."id";


--
-- TOC entry 264 (class 1259 OID 30489)
-- Name: event_tasks; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."event_tasks" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "reservation_id" bigint NOT NULL,
    "title" character varying(255) NOT NULL,
    "category" character varying(255) DEFAULT 'salle_mobilier'::character varying NOT NULL,
    "timing_phase" character varying(255) DEFAULT 'DAY_OF_SETUP'::character varying NOT NULL,
    "status" character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    "is_critical" boolean DEFAULT false NOT NULL,
    "assigned_to" bigint,
    "completed_at" timestamp(0) without time zone,
    "completed_by" bigint,
    "notes" "text",
    "sort_order" integer DEFAULT 0 NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."event_tasks" OWNER TO "ITAdmin";

--
-- TOC entry 263 (class 1259 OID 30488)
-- Name: event_tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."event_tasks_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."event_tasks_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5436 (class 0 OID 0)
-- Dependencies: 263
-- Name: event_tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."event_tasks_id_seq" OWNED BY "public"."event_tasks"."id";


--
-- TOC entry 268 (class 1259 OID 30562)
-- Name: event_timelines; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."event_timelines" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "reservation_id" bigint NOT NULL,
    "time" character varying(255) NOT NULL,
    "title" character varying(255) NOT NULL,
    "description" "text",
    "person_in_charge" character varying(255),
    "is_done" boolean DEFAULT false NOT NULL,
    "sort_order" integer DEFAULT 0 NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."event_timelines" OWNER TO "ITAdmin";

--
-- TOC entry 267 (class 1259 OID 30561)
-- Name: event_timelines_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."event_timelines_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."event_timelines_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5437 (class 0 OID 0)
-- Dependencies: 267
-- Name: event_timelines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."event_timelines_id_seq" OWNED BY "public"."event_timelines"."id";


--
-- TOC entry 266 (class 1259 OID 30534)
-- Name: event_vendors; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."event_vendors" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "reservation_id" bigint NOT NULL,
    "category" character varying(255) NOT NULL,
    "company_name" character varying(255) NOT NULL,
    "contact_person" character varying(255),
    "phone" character varying(255) NOT NULL,
    "arrival_time" character varying(255),
    "special_notes" "text",
    "is_confirmed" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."event_vendors" OWNER TO "ITAdmin";

--
-- TOC entry 265 (class 1259 OID 30533)
-- Name: event_vendors_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."event_vendors_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."event_vendors_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5438 (class 0 OID 0)
-- Dependencies: 265
-- Name: event_vendors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."event_vendors_id_seq" OWNED BY "public"."event_vendors"."id";


--
-- TOC entry 256 (class 1259 OID 30354)
-- Name: expense_categories; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."expense_categories" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "name_fr" character varying(100) NOT NULL,
    "name_ar" character varying(100),
    "color" character varying(20) DEFAULT '#64748b'::character varying NOT NULL,
    "is_active" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."expense_categories" OWNER TO "ITAdmin";

--
-- TOC entry 255 (class 1259 OID 30353)
-- Name: expense_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."expense_categories_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."expense_categories_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5439 (class 0 OID 0)
-- Dependencies: 255
-- Name: expense_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."expense_categories_id_seq" OWNED BY "public"."expense_categories"."id";


--
-- TOC entry 258 (class 1259 OID 30373)
-- Name: expenses; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."expenses" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "expense_category_id" bigint,
    "created_by" bigint NOT NULL,
    "reference" character varying(50),
    "vendor" character varying(150),
    "description" "text" NOT NULL,
    "amount" numeric(12,2) NOT NULL,
    "expense_date" "date" NOT NULL,
    "payment_method" character varying(20) DEFAULT 'CASH'::character varying NOT NULL,
    "cheque_number" character varying(100),
    "bank_name" character varying(100),
    "notes" "text",
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."expenses" OWNER TO "ITAdmin";

--
-- TOC entry 257 (class 1259 OID 30372)
-- Name: expenses_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."expenses_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."expenses_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5440 (class 0 OID 0)
-- Dependencies: 257
-- Name: expenses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."expenses_id_seq" OWNED BY "public"."expenses"."id";


--
-- TOC entry 233 (class 1259 OID 29888)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."failed_jobs" (
    "id" bigint NOT NULL,
    "uuid" character varying(255) NOT NULL,
    "connection" character varying(255) NOT NULL,
    "queue" character varying(255) NOT NULL,
    "payload" "text" NOT NULL,
    "exception" "text" NOT NULL,
    "failed_at" timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE "public"."failed_jobs" OWNER TO "ITAdmin";

--
-- TOC entry 232 (class 1259 OID 29887)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."failed_jobs_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."failed_jobs_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5441 (class 0 OID 0)
-- Dependencies: 232
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."failed_jobs_id_seq" OWNED BY "public"."failed_jobs"."id";


--
-- TOC entry 260 (class 1259 OID 30420)
-- Name: inventory_items; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."inventory_items" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "name" character varying(255) NOT NULL,
    "category" character varying(255) NOT NULL,
    "unit" character varying(255) DEFAULT 'pcs'::character varying NOT NULL,
    "quantity_total" integer DEFAULT 0 NOT NULL,
    "quantity_available" integer DEFAULT 0 NOT NULL,
    "quantity_alert" integer DEFAULT 5 NOT NULL,
    "description" "text",
    "location" character varying(255),
    "unit_cost" numeric(12,2),
    "is_active" boolean DEFAULT true NOT NULL,
    "created_by" bigint,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."inventory_items" OWNER TO "ITAdmin";

--
-- TOC entry 259 (class 1259 OID 30419)
-- Name: inventory_items_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."inventory_items_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."inventory_items_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5442 (class 0 OID 0)
-- Dependencies: 259
-- Name: inventory_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."inventory_items_id_seq" OWNED BY "public"."inventory_items"."id";


--
-- TOC entry 262 (class 1259 OID 30453)
-- Name: inventory_movements; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."inventory_movements" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "inventory_item_id" bigint NOT NULL,
    "reservation_id" bigint,
    "type" character varying(255) NOT NULL,
    "quantity" integer NOT NULL,
    "quantity_before" integer NOT NULL,
    "quantity_after" integer NOT NULL,
    "reason" character varying(255),
    "notes" "text",
    "created_by" bigint,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."inventory_movements" OWNER TO "ITAdmin";

--
-- TOC entry 261 (class 1259 OID 30452)
-- Name: inventory_movements_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."inventory_movements_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."inventory_movements_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5443 (class 0 OID 0)
-- Dependencies: 261
-- Name: inventory_movements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."inventory_movements_id_seq" OWNED BY "public"."inventory_movements"."id";


--
-- TOC entry 231 (class 1259 OID 29873)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."job_batches" (
    "id" character varying(255) NOT NULL,
    "name" character varying(255) NOT NULL,
    "total_jobs" integer NOT NULL,
    "pending_jobs" integer NOT NULL,
    "failed_jobs" integer NOT NULL,
    "failed_job_ids" "text" NOT NULL,
    "options" "text",
    "cancelled_at" integer,
    "created_at" integer NOT NULL,
    "finished_at" integer
);


ALTER TABLE "public"."job_batches" OWNER TO "ITAdmin";

--
-- TOC entry 230 (class 1259 OID 29858)
-- Name: jobs; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."jobs" (
    "id" bigint NOT NULL,
    "queue" character varying(255) NOT NULL,
    "payload" "text" NOT NULL,
    "attempts" smallint NOT NULL,
    "reserved_at" integer,
    "available_at" integer NOT NULL,
    "created_at" integer NOT NULL
);


ALTER TABLE "public"."jobs" OWNER TO "ITAdmin";

--
-- TOC entry 229 (class 1259 OID 29857)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."jobs_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."jobs_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5444 (class 0 OID 0)
-- Dependencies: 229
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."jobs_id_seq" OWNED BY "public"."jobs"."id";


--
-- TOC entry 220 (class 1259 OID 29753)
-- Name: migrations; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."migrations" (
    "id" integer NOT NULL,
    "migration" character varying(255) NOT NULL,
    "batch" integer NOT NULL
);


ALTER TABLE "public"."migrations" OWNER TO "ITAdmin";

--
-- TOC entry 219 (class 1259 OID 29752)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."migrations_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."migrations_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5445 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."migrations_id_seq" OWNED BY "public"."migrations"."id";


--
-- TOC entry 250 (class 1259 OID 30182)
-- Name: packages; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."packages" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "name_fr" character varying(150) NOT NULL,
    "name_ar" character varying(150),
    "description_fr" "text",
    "description_ar" "text",
    "base_price" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "price_per_guest" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "included_items" json,
    "is_active" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."packages" OWNER TO "ITAdmin";

--
-- TOC entry 249 (class 1259 OID 30181)
-- Name: packages_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."packages_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."packages_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5446 (class 0 OID 0)
-- Dependencies: 249
-- Name: packages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."packages_id_seq" OWNED BY "public"."packages"."id";


--
-- TOC entry 227 (class 1259 OID 29836)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."password_reset_tokens" (
    "email" character varying(255) NOT NULL,
    "token" character varying(255) NOT NULL,
    "created_at" timestamp(0) without time zone
);


ALTER TABLE "public"."password_reset_tokens" OWNER TO "ITAdmin";

--
-- TOC entry 254 (class 1259 OID 30312)
-- Name: payments; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."payments" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "reservation_id" bigint NOT NULL,
    "cash_register_id" bigint,
    "receipt_number" character varying(50) NOT NULL,
    "payment_type" character varying(30) NOT NULL,
    "payment_method" character varying(30) NOT NULL,
    "amount" numeric(12,2) NOT NULL,
    "cheque_number" character varying(100),
    "bank_name" character varying(100),
    "payment_date" "date" NOT NULL,
    "notes" "text",
    "received_by" bigint NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."payments" OWNER TO "ITAdmin";

--
-- TOC entry 253 (class 1259 OID 30311)
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."payments_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."payments_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5447 (class 0 OID 0)
-- Dependencies: 253
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."payments_id_seq" OWNED BY "public"."payments"."id";


--
-- TOC entry 270 (class 1259 OID 30591)
-- Name: quotations; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."quotations" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "quote_number" character varying(50) NOT NULL,
    "customer_id" bigint,
    "customer_name" character varying(150) NOT NULL,
    "customer_phone" character varying(30) NOT NULL,
    "bride_name" character varying(120),
    "groom_name" character varying(120),
    "venue_space_id" bigint NOT NULL,
    "event_date" "date" NOT NULL,
    "start_time" time(0) without time zone NOT NULL,
    "end_time" time(0) without time zone NOT NULL,
    "expected_guests" integer DEFAULT 100 NOT NULL,
    "package_id" bigint,
    "total_price" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "deposit_required" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "status" character varying(30) DEFAULT 'DRAFT'::character varying NOT NULL,
    "valid_until" "date" NOT NULL,
    "notes" "text",
    "converted_reservation_id" bigint,
    "created_by" bigint,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone,
    "subtotal" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "discount_type" character varying(20) DEFAULT 'none'::character varying NOT NULL,
    "discount_value" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "discount_amount" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "tax_rate" numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    "tax_amount" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "items" json,
    "payment_terms" "text"
);


ALTER TABLE "public"."quotations" OWNER TO "ITAdmin";

--
-- TOC entry 269 (class 1259 OID 30590)
-- Name: quotations_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."quotations_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."quotations_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5448 (class 0 OID 0)
-- Dependencies: 269
-- Name: quotations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."quotations_id_seq" OWNED BY "public"."quotations"."id";


--
-- TOC entry 243 (class 1259 OID 30010)
-- Name: reservations; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."reservations" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "reservation_number" character varying(30) NOT NULL,
    "venue_space_id" bigint NOT NULL,
    "time_slot_id" bigint,
    "customer_name" character varying(150) NOT NULL,
    "customer_phone" character varying(30) NOT NULL,
    "bride_name" character varying(120),
    "groom_name" character varying(120),
    "event_type" character varying(50) DEFAULT 'MARIAGE'::character varying NOT NULL,
    "event_date" "date" NOT NULL,
    "start_time" time(0) without time zone NOT NULL,
    "end_time" time(0) without time zone NOT NULL,
    "expected_guests" integer DEFAULT 100 NOT NULL,
    "status" character varying(30) DEFAULT 'INQUIRY'::character varying NOT NULL,
    "payment_status" character varying(30) DEFAULT 'UNPAID'::character varying NOT NULL,
    "hold_expires_at" timestamp(0) without time zone,
    "total_price" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "deposit_required" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "deposit_paid" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "total_paid" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "remaining_balance" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "internal_notes" "text",
    "created_by" bigint,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone,
    "customer_id" bigint,
    "cancelled_at" timestamp(0) without time zone,
    "cancellation_reason" "text",
    "cancelled_by" bigint,
    "refund_amount" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "package_id" bigint
);


ALTER TABLE "public"."reservations" OWNER TO "ITAdmin";

--
-- TOC entry 242 (class 1259 OID 30009)
-- Name: reservations_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."reservations_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."reservations_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5449 (class 0 OID 0)
-- Dependencies: 242
-- Name: reservations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."reservations_id_seq" OWNED BY "public"."reservations"."id";


--
-- TOC entry 228 (class 1259 OID 29845)
-- Name: sessions; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."sessions" (
    "id" character varying(255) NOT NULL,
    "user_id" bigint,
    "ip_address" character varying(45),
    "user_agent" "text",
    "payload" "text" NOT NULL,
    "last_activity" integer NOT NULL
);


ALTER TABLE "public"."sessions" OWNER TO "ITAdmin";

--
-- TOC entry 241 (class 1259 OID 29986)
-- Name: time_slots; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."time_slots" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "name_fr" character varying(100) NOT NULL,
    "name_ar" character varying(100) NOT NULL,
    "start_time" time(0) without time zone NOT NULL,
    "end_time" time(0) without time zone NOT NULL,
    "is_overnight" boolean DEFAULT false NOT NULL,
    "sort_order" smallint DEFAULT '1'::smallint NOT NULL,
    "is_active" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."time_slots" OWNER TO "ITAdmin";

--
-- TOC entry 240 (class 1259 OID 29985)
-- Name: time_slots_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."time_slots_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."time_slots_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5450 (class 0 OID 0)
-- Dependencies: 240
-- Name: time_slots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."time_slots_id_seq" OWNED BY "public"."time_slots"."id";


--
-- TOC entry 226 (class 1259 OID 29811)
-- Name: users; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."users" (
    "id" bigint NOT NULL,
    "business_id" bigint,
    "name" character varying(255) NOT NULL,
    "email" character varying(255) NOT NULL,
    "phone" character varying(30),
    "role" character varying(30) DEFAULT 'RECEPTIONIST'::character varying NOT NULL,
    "preferred_language" character varying(5) DEFAULT 'fr'::character varying NOT NULL,
    "is_active" boolean DEFAULT true NOT NULL,
    "email_verified_at" timestamp(0) without time zone,
    "password" character varying(255) NOT NULL,
    "remember_token" character varying(100),
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."users" OWNER TO "ITAdmin";

--
-- TOC entry 225 (class 1259 OID 29810)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."users_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."users_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5451 (class 0 OID 0)
-- Dependencies: 225
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."users_id_seq" OWNED BY "public"."users"."id";


--
-- TOC entry 239 (class 1259 OID 29956)
-- Name: venue_spaces; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."venue_spaces" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "venue_id" bigint NOT NULL,
    "name_fr" character varying(150) NOT NULL,
    "name_ar" character varying(150) NOT NULL,
    "capacity_min" integer DEFAULT 50 NOT NULL,
    "capacity_max" integer DEFAULT 450 NOT NULL,
    "base_price" numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    "is_active" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."venue_spaces" OWNER TO "ITAdmin";

--
-- TOC entry 238 (class 1259 OID 29955)
-- Name: venue_spaces_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."venue_spaces_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."venue_spaces_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5452 (class 0 OID 0)
-- Dependencies: 238
-- Name: venue_spaces_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."venue_spaces_id_seq" OWNED BY "public"."venue_spaces"."id";


--
-- TOC entry 237 (class 1259 OID 29936)
-- Name: venues; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."venues" (
    "id" bigint NOT NULL,
    "business_id" bigint NOT NULL,
    "name_fr" character varying(150) NOT NULL,
    "name_ar" character varying(150) NOT NULL,
    "address" "text",
    "is_active" boolean DEFAULT true NOT NULL,
    "created_at" timestamp(0) without time zone,
    "updated_at" timestamp(0) without time zone
);


ALTER TABLE "public"."venues" OWNER TO "ITAdmin";

--
-- TOC entry 236 (class 1259 OID 29935)
-- Name: venues_id_seq; Type: SEQUENCE; Schema: public; Owner: ITAdmin
--

CREATE SEQUENCE "public"."venues_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE "public"."venues_id_seq" OWNER TO "ITAdmin";

--
-- TOC entry 5453 (class 0 OID 0)
-- Dependencies: 236
-- Name: venues_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ITAdmin
--

ALTER SEQUENCE "public"."venues_id_seq" OWNED BY "public"."venues"."id";


--
-- TOC entry 244 (class 1259 OID 30069)
-- Name: wilayas; Type: TABLE; Schema: public; Owner: ITAdmin
--

CREATE TABLE "public"."wilayas" (
    "id" smallint NOT NULL,
    "code" character varying(3) NOT NULL,
    "name_fr" character varying(100) NOT NULL,
    "name_ar" character varying(100) NOT NULL
);


ALTER TABLE "public"."wilayas" OWNER TO "ITAdmin";

--
-- TOC entry 5004 (class 2604 OID 29911)
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."audit_logs" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."audit_logs_id_seq"'::"regclass");


--
-- TOC entry 4991 (class 2604 OID 29766)
-- Name: businesses id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."businesses" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."businesses_id_seq"'::"regclass");


--
-- TOC entry 5036 (class 2604 OID 30275)
-- Name: cash_registers id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cash_registers" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."cash_registers_id_seq"'::"regclass");


--
-- TOC entry 5028 (class 2604 OID 30084)
-- Name: communes id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."communes" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."communes_id_seq"'::"regclass");


--
-- TOC entry 5029 (class 2604 OID 30101)
-- Name: customers id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."customers" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."customers_id_seq"'::"regclass");


--
-- TOC entry 5055 (class 2604 OID 30492)
-- Name: event_tasks id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_tasks" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."event_tasks_id_seq"'::"regclass");


--
-- TOC entry 5063 (class 2604 OID 30565)
-- Name: event_timelines id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_timelines" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."event_timelines_id_seq"'::"regclass");


--
-- TOC entry 5061 (class 2604 OID 30537)
-- Name: event_vendors id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_vendors" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."event_vendors_id_seq"'::"regclass");


--
-- TOC entry 5043 (class 2604 OID 30357)
-- Name: expense_categories id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expense_categories" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."expense_categories_id_seq"'::"regclass");


--
-- TOC entry 5046 (class 2604 OID 30376)
-- Name: expenses id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expenses" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."expenses_id_seq"'::"regclass");


--
-- TOC entry 5002 (class 2604 OID 29891)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."failed_jobs" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."failed_jobs_id_seq"'::"regclass");


--
-- TOC entry 5048 (class 2604 OID 30423)
-- Name: inventory_items id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_items" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."inventory_items_id_seq"'::"regclass");


--
-- TOC entry 5054 (class 2604 OID 30456)
-- Name: inventory_movements id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_movements" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."inventory_movements_id_seq"'::"regclass");


--
-- TOC entry 5001 (class 2604 OID 29861)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."jobs" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."jobs_id_seq"'::"regclass");


--
-- TOC entry 4990 (class 2604 OID 29756)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."migrations" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."migrations_id_seq"'::"regclass");


--
-- TOC entry 5032 (class 2604 OID 30185)
-- Name: packages id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."packages" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."packages_id_seq"'::"regclass");


--
-- TOC entry 5042 (class 2604 OID 30315)
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."payments_id_seq"'::"regclass");


--
-- TOC entry 5066 (class 2604 OID 30594)
-- Name: quotations id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."quotations_id_seq"'::"regclass");


--
-- TOC entry 5017 (class 2604 OID 30013)
-- Name: reservations id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."reservations_id_seq"'::"regclass");


--
-- TOC entry 5013 (class 2604 OID 29989)
-- Name: time_slots id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."time_slots" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."time_slots_id_seq"'::"regclass");


--
-- TOC entry 4997 (class 2604 OID 29814)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."users" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."users_id_seq"'::"regclass");


--
-- TOC entry 5008 (class 2604 OID 29959)
-- Name: venue_spaces id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venue_spaces" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."venue_spaces_id_seq"'::"regclass");


--
-- TOC entry 5006 (class 2604 OID 29939)
-- Name: venues id; Type: DEFAULT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venues" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."venues_id_seq"'::"regclass");


--
-- TOC entry 5388 (class 0 OID 29908)
-- Dependencies: 235
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (1, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 21:19:48') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (2, 1, 1, 'STATUS_CHANGE', 'App\Models\Reservation', 3, '{"status":"CONFIRMED"}', '{"status":"CANCELLED"}', '127.0.0.1', NULL, '2026-09-25 21:23:09') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (3, 1, 1, 'STATUS_CHANGE', 'App\Models\Reservation', 2, '{"status":"TEMPORARY_HOLD"}', '{"status":"CANCELLED"}', '127.0.0.1', NULL, '2026-09-25 21:27:04') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (4, 1, 1, 'STATUS_CHANGE', 'App\Models\Reservation', 1, '{"status":"CONFIRMED"}', '{"status":"CANCELLED"}', '127.0.0.1', NULL, '2026-09-25 21:27:21') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (5, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 21:40:35') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (6, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:32:52') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (7, 1, 1, 'LOGOUT', 'App\Models\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:33:29') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (8, 1, 2, 'LOGIN', 'App\Models\User', 2, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:33:35') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (9, 1, 2, 'LOGOUT', 'App\Models\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:33:42') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (10, 1, 3, 'LOGIN', 'App\Models\User', 3, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:33:46') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (11, 1, 3, 'LOGOUT', 'App\Models\User', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:40:42') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (12, 1, 4, 'LOGIN', 'App\Models\User', 4, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:40:47') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (13, 1, 4, 'LOGOUT', 'App\Models\User', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:41:27') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (14, 1, 5, 'LOGIN', 'App\Models\User', 5, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:41:33') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (15, 1, 5, 'LOGOUT', 'App\Models\User', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:41:53') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (16, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 01:42:00') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (17, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 02:05:08') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (18, 1, 1, 'CREATE', 'App\Models\Reservation', 4, NULL, '{"number":"RES-2026-0001","status":"CONFIRMED"}', '127.0.0.1', NULL, '2026-09-26 02:06:18') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (19, 1, 1, 'STATUS_CHANGE', 'App\Models\Reservation', 4, '{"status":"CONFIRMED"}', '{"status":"CANCELLED"}', '127.0.0.1', NULL, '2026-09-26 02:06:46') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (20, 1, 1, 'LOGOUT', 'App\Models\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 02:40:35') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (21, 1, 5, 'LOGIN', 'App\Models\User', 5, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 02:40:42') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (22, 1, 5, 'LOGOUT', 'App\Models\User', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 02:41:06') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (23, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 18:14:15') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (24, 1, 1, 'CREATE', 'App\Models\Reservation', 5, NULL, '{"number":"RES-2026-0002","status":"CONFIRMED"}', '127.0.0.1', NULL, '2026-09-26 19:11:32') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (25, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 20:34:13') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (26, 1, 1, 'STATUS_CHANGE', 'App\Models\Reservation', 5, '{"status":"CONFIRMED"}', '{"status":"CANCELLED"}', '127.0.0.1', NULL, '2026-09-26 20:59:41') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (27, 1, 1, 'LOGOUT', 'App\Models\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 21:07:45') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (28, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 21:12:43') ON CONFLICT DO NOTHING;
INSERT INTO "public"."audit_logs" ("id", "business_id", "user_id", "action", "auditable_type", "auditable_id", "old_values", "new_values", "ip_address", "user_agent", "created_at") VALUES (29, 1, 1, 'LOGIN', 'App\Models\User', 1, NULL, '{"ip":"127.0.0.1"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-26 23:42:29') ON CONFLICT DO NOTHING;


--
-- TOC entry 5375 (class 0 OID 29763)
-- Dependencies: 222
-- Data for Name: businesses; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."businesses" ("id", "name_fr", "name_ar", "slug", "phone", "whatsapp", "email", "wilaya", "commune", "address_line", "nif", "nis", "rc", "logo_path", "currency", "timezone", "settings", "is_active", "created_at", "updated_at") VALUES (1, 'Palais El Bahia', 'قصر الباهية للأفراح والمناسبات', 'palais-el-bahia', '0550 12 34 56', '0550 12 34 56', 'contact@palaiselbahia.dz', 'Alger', 'Alger Centre', '14 Boulevard des Martyrs, Alger', '099816001234567', '1600123456789', '16/00-1234567B22', NULL, 'DZD', 'Africa/Algiers', '{"contract_notice_days":15,"cleaning_buffer_minutes":60,"default_deposit_percentage":30}', true, '2026-09-25 18:54:24', '2026-09-25 18:54:24') ON CONFLICT DO NOTHING;


--
-- TOC entry 5376 (class 0 OID 29788)
-- Dependencies: 223
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5377 (class 0 OID 29799)
-- Dependencies: 224
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5405 (class 0 OID 30272)
-- Dependencies: 252
-- Data for Name: cash_registers; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."cash_registers" ("id", "business_id", "register_date", "opening_balance", "total_cash_in", "total_cash_out", "expected_closing_balance", "actual_closing_balance", "status", "opened_by", "closed_by", "opened_at", "closed_at", "notes", "created_at", "updated_at") VALUES (1, 1, '2026-09-25', 0.00, 0.00, 0.00, 0.00, 0.00, 'CLOSED', 1, 4, '2026-09-25 20:48:34', '2026-09-26 00:41:12', '', '2026-09-25 20:48:34', '2026-09-26 00:41:12') ON CONFLICT DO NOTHING;


--
-- TOC entry 5399 (class 0 OID 30081)
-- Dependencies: 246
-- Data for Name: communes; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (1, 16, 'Alger Centre', 'الجزائر الوسطى', '16001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (2, 16, 'Bab El Oued', 'باب الواد', '16002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (3, 16, 'Hussein Dey', 'حسين داي', '16003') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (4, 16, 'El Harrach', 'الحراش', '16004') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (5, 16, 'Kouba', 'القبة', '16005') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (6, 16, 'Bir Mourad Raïs', 'بئر مراد رايس', '16006') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (7, 16, 'Hydra', 'حيدرة', '16007') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (8, 16, 'Ben Aknoun', 'بن عكنون', '16008') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (9, 16, 'Birkhadem', 'بئر خادم', '16009') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (10, 16, 'Chéraga', 'شراقة', '16010') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (11, 16, 'Bab Ezzouar', 'باب الزوار', '16011') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (12, 16, 'Dar El Beïda', 'دار البيضاء', '16012') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (13, 16, 'Rouiba', 'الرويبة', '16013') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (14, 16, 'Reghaia', 'الرغاية', '16014') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (15, 16, 'Bordj El Kiffan', 'برج الكيفان', '16015') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (16, 31, 'Oran', 'وهران', '31001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (17, 31, 'Bir El Djir', 'بئر الجير', '31002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (18, 31, 'Es Sénia', 'السانية', '31003') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (19, 31, 'Arzew', 'أرزيو', '31004') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (20, 25, 'Constantine', 'قسنطينة', '25001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (21, 25, 'El Khroub', 'الخروب', '25002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (22, 25, 'Hamma Bouziane', 'حامة بوزيان', '25003') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (23, 23, 'Annaba', 'عنابة', '23001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (24, 23, 'El Bouni', 'البوني', '23002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (25, 9, 'Blida', 'البليدة', '09001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (26, 9, 'Boufarik', 'بوفاريك', '09002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (27, 9, 'Meftah', 'مفتاح', '09003') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (28, 19, 'Sétif', 'سطيف', '19001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (29, 19, 'El Eulma', 'العلمة', '19002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (30, 15, 'Tizi Ouzou', 'تيزي وزو', '15001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (31, 15, 'Azazga', 'عزازقة', '15002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (32, 6, 'Béjaïa', 'بجاية', '06001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (33, 6, 'Kherrata', 'خراطة', '06002') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (34, 5, 'Batna', 'باتنة', '05001') ON CONFLICT DO NOTHING;
INSERT INTO "public"."communes" ("id", "wilaya_id", "name_fr", "name_ar", "postcode") VALUES (35, 5, 'Arris', 'أريس', '05002') ON CONFLICT DO NOTHING;


--
-- TOC entry 5401 (class 0 OID 30098)
-- Dependencies: 248
-- Data for Name: customers; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."customers" ("id", "business_id", "customer_code", "full_name", "phone", "second_phone", "whatsapp_number", "email", "wilaya_id", "commune_id", "address_line", "bride_name", "groom_name", "preferred_language", "source", "internal_notes", "created_at", "updated_at") VALUES (1, 1, 'CLT-6AB71A89D1036', 'sdsd', '2132132', NULL, NULL, NULL, 44, NULL, NULL, 'dddd', 'ddd', 'fr', 'WALK_IN', NULL, '2026-09-26 01:06:17', '2026-09-26 01:06:17') ON CONFLICT DO NOTHING;
INSERT INTO "public"."customers" ("id", "business_id", "customer_code", "full_name", "phone", "second_phone", "whatsapp_number", "email", "wilaya_id", "commune_id", "address_line", "bride_name", "groom_name", "preferred_language", "source", "internal_notes", "created_at", "updated_at") VALUES (2, 1, 'CLT-6AB80AD386700', 'jhgjhg', '654665', NULL, NULL, NULL, 7, NULL, NULL, 'jhjh', 'kk', 'fr', 'WALK_IN', NULL, '2026-09-26 18:11:31', '2026-09-26 18:11:31') ON CONFLICT DO NOTHING;


--
-- TOC entry 5417 (class 0 OID 30489)
-- Dependencies: 264
-- Data for Name: event_tasks; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (7, 1, 4, 'Vérification et allumage de la climatisation / chauffage (T° idéale 22°C)', 'technique', 'DAY_OF_SETUP', 'pending', true, 2, NULL, NULL, NULL, 7, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (8, 1, 4, 'Arrivée du traiteur : contrôle propreté cuisine, armoires réfrigérées et accès', 'traiteur', 'DAY_OF_SETUP', 'pending', true, 2, NULL, NULL, NULL, 8, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (9, 1, 4, 'Mise en place de la vaisselle, verres cristal et couverts sur les tables', 'traiteur', 'DAY_OF_SETUP', 'pending', false, 2, NULL, NULL, NULL, 9, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (10, 1, 4, 'Installation des centres de table (candélabres dorés / compositions florales)', 'decoration', 'DAY_OF_SETUP', 'pending', false, 2, NULL, NULL, NULL, 10, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (11, 1, 4, 'Accueil et briefing des agents de sécurité & hôtesse vestiaire', 'accueil_securite', 'DURING_EVENT', 'pending', true, 2, NULL, NULL, NULL, 11, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (12, 1, 4, 'Contrôle permanent de la propreté et du réapprovisionnement des sanitaires', 'accueil_securite', 'DURING_EVENT', 'pending', true, 2, NULL, NULL, NULL, 12, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (13, 1, 4, 'Coordination arrivée du cortège mariés avec le DJ et machine à étincelles / fumée', 'technique', 'DURING_EVENT', 'pending', false, 2, NULL, NULL, NULL, 13, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (14, 1, 4, 'Règlement du solde restant auprès du responsable de famille avant le gâteau', 'accueil_securite', 'DURING_EVENT', 'pending', true, 2, NULL, NULL, NULL, 14, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (15, 1, 4, 'Comptage retour du matériel loué (vaisselle, nappes, sono) et état des lieux sortie', 'cloture_nettoyage', 'POST_EVENT', 'pending', true, 2, NULL, NULL, NULL, 15, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (2, 1, 4, 'Disposition et alignement des tables rondes et chaises selon plan de salle', 'salle_mobilier', 'J_MINUS_1', 'completed', true, 2, '2026-09-26 17:40:31', 2, NULL, 2, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (1, 1, 4, 'Nettoyage approfondi de la salle, sanitaires et office traiteur', 'cloture_nettoyage', 'J_MINUS_1', 'completed', true, 2, '2026-09-26 17:40:31', 2, NULL, 1, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (6, 1, 4, 'Vérification groupe électrogène / secours électrique et réserves de carburant', 'technique', 'DAY_OF_SETUP', 'completed', true, 2, '2026-09-26 17:40:31', 2, NULL, 6, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (5, 1, 4, 'Test complet sonorisation, micros HF (piles neuves) et jeux de lumières / PAR LED', 'technique', 'DAY_OF_SETUP', 'completed', true, 2, '2026-09-26 17:40:31', 2, NULL, 5, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (3, 1, 4, 'Repassage et mise en place des nappes satin et housses/galettes de chaises', 'salle_mobilier', 'J_MINUS_1', 'completed', false, 2, '2026-09-26 17:40:31', 2, NULL, 3, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (4, 1, 4, 'Installation de l''estrade d''honneur, Kosha (trône des mariés) et arche', 'decoration', 'J_MINUS_1', 'completed', true, 2, '2026-09-26 17:40:31', 2, NULL, 4, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (17, 1, 5, 'Disposition et alignement des tables rondes et chaises selon plan de salle', 'salle_mobilier', 'J_MINUS_1', 'pending', true, 2, NULL, NULL, NULL, 2, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (18, 1, 5, 'Repassage et mise en place des nappes satin et housses/galettes de chaises', 'salle_mobilier', 'J_MINUS_1', 'pending', false, 2, NULL, NULL, NULL, 3, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (19, 1, 5, 'Installation de l''estrade d''honneur, Kosha (trône des mariés) et arche', 'decoration', 'J_MINUS_1', 'pending', true, 2, NULL, NULL, NULL, 4, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (20, 1, 5, 'Test complet sonorisation, micros HF (piles neuves) et jeux de lumières / PAR LED', 'technique', 'DAY_OF_SETUP', 'pending', true, 2, NULL, NULL, NULL, 5, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (21, 1, 5, 'Vérification groupe électrogène / secours électrique et réserves de carburant', 'technique', 'DAY_OF_SETUP', 'pending', true, 2, NULL, NULL, NULL, 6, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (22, 1, 5, 'Vérification et allumage de la climatisation / chauffage (T° idéale 22°C)', 'technique', 'DAY_OF_SETUP', 'pending', true, 2, NULL, NULL, NULL, 7, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (23, 1, 5, 'Arrivée du traiteur : contrôle propreté cuisine, armoires réfrigérées et accès', 'traiteur', 'DAY_OF_SETUP', 'pending', true, 2, NULL, NULL, NULL, 8, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (24, 1, 5, 'Mise en place de la vaisselle, verres cristal et couverts sur les tables', 'traiteur', 'DAY_OF_SETUP', 'pending', false, 2, NULL, NULL, NULL, 9, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (25, 1, 5, 'Installation des centres de table (candélabres dorés / compositions florales)', 'decoration', 'DAY_OF_SETUP', 'pending', false, 2, NULL, NULL, NULL, 10, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (26, 1, 5, 'Accueil et briefing des agents de sécurité & hôtesse vestiaire', 'accueil_securite', 'DURING_EVENT', 'pending', true, 2, NULL, NULL, NULL, 11, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (27, 1, 5, 'Contrôle permanent de la propreté et du réapprovisionnement des sanitaires', 'accueil_securite', 'DURING_EVENT', 'pending', true, 2, NULL, NULL, NULL, 12, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (28, 1, 5, 'Coordination arrivée du cortège mariés avec le DJ et machine à étincelles / fumée', 'technique', 'DURING_EVENT', 'pending', false, 2, NULL, NULL, NULL, 13, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (29, 1, 5, 'Règlement du solde restant auprès du responsable de famille avant le gâteau', 'accueil_securite', 'DURING_EVENT', 'pending', true, 2, NULL, NULL, NULL, 14, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (30, 1, 5, 'Comptage retour du matériel loué (vaisselle, nappes, sono) et état des lieux sortie', 'cloture_nettoyage', 'POST_EVENT', 'pending', true, 2, NULL, NULL, NULL, 15, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_tasks" ("id", "business_id", "reservation_id", "title", "category", "timing_phase", "status", "is_critical", "assigned_to", "completed_at", "completed_by", "notes", "sort_order", "created_at", "updated_at") VALUES (16, 1, 5, 'Nettoyage approfondi de la salle, sanitaires et office traiteur', 'cloture_nettoyage', 'J_MINUS_1', 'pending', true, 2, NULL, NULL, NULL, 1, '2026-09-26 19:40:31', '2026-09-26 19:56:36') ON CONFLICT DO NOTHING;


--
-- TOC entry 5421 (class 0 OID 30562)
-- Dependencies: 268
-- Data for Name: event_timelines; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (2, 1, 4, '17:30', 'Arrivée de la famille proche et vérification finale', 'Accueil des parents, remise des clés loge mariés et consignes', 'Coordinatrice', false, 2, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (3, 1, 4, '18:30', 'Ouverture des portes aux invités & Service thé/gâteaux', 'Accueil à l''entrée, orientation vers les tables et musique d''ambiance douce', 'Équipe accueil & Serveurs', false, 3, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (4, 1, 4, '20:30', 'Entrée triomphale du cortège & Mariés (Dakhla)', 'Activation fumée lourde, étincelles froides et musique d''entrée DJ', 'DJ & Régie technique', false, 4, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (5, 1, 4, '21:45', 'Service du Dîner principal', 'Service aux tables ou ouverture des buffets chauds', 'Chef Traiteur', false, 5, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (6, 1, 4, '23:30', 'Cérémonie de la Pièce Montée (Gâteau d''honneur)', 'Changement de tenue des mariés, musique gâteau et séance photos', 'Photographe & DJ', false, 6, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (7, 1, 4, '01:30', 'Fin de soirée & Départ des invités', 'Baisse progressive de la musique et fermeture des accès extérieurs', 'Sécurité', false, 7, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (1, 1, 4, '15:00', 'Accès salle pour Décorateur, DJ et Traiteur', 'Installation matériel son, vérification prises et mise en chauffe cuisines', 'Régisseur de salle', true, 1, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (8, 1, 5, '15:00', 'Accès salle pour Décorateur, DJ et Traiteur', 'Installation matériel son, vérification prises et mise en chauffe cuisines', 'Régisseur de salle', false, 1, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (9, 1, 5, '17:30', 'Arrivée de la famille proche et vérification finale', 'Accueil des parents, remise des clés loge mariés et consignes', 'Coordinatrice', false, 2, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (10, 1, 5, '18:30', 'Ouverture des portes aux invités & Service thé/gâteaux', 'Accueil à l''entrée, orientation vers les tables et musique d''ambiance douce', 'Équipe accueil & Serveurs', false, 3, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (11, 1, 5, '20:30', 'Entrée triomphale du cortège & Mariés (Dakhla)', 'Activation fumée lourde, étincelles froides et musique d''entrée DJ', 'DJ & Régie technique', false, 4, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (12, 1, 5, '21:45', 'Service du Dîner principal', 'Service aux tables ou ouverture des buffets chauds', 'Chef Traiteur', false, 5, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (13, 1, 5, '23:30', 'Cérémonie de la Pièce Montée (Gâteau d''honneur)', 'Changement de tenue des mariés, musique gâteau et séance photos', 'Photographe & DJ', false, 6, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_timelines" ("id", "business_id", "reservation_id", "time", "title", "description", "person_in_charge", "is_done", "sort_order", "created_at", "updated_at") VALUES (14, 1, 5, '01:30', 'Fin de soirée & Départ des invités', 'Baisse progressive de la musique et fermeture des accès extérieurs', 'Sécurité', false, 7, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;


--
-- TOC entry 5419 (class 0 OID 30534)
-- Dependencies: 266
-- Data for Name: event_vendors; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."event_vendors" ("id", "business_id", "reservation_id", "category", "company_name", "contact_person", "phone", "arrival_time", "special_notes", "is_confirmed", "created_at", "updated_at") VALUES (1, 1, 4, 'traiteur', 'Traiteur Royal El Mordjene', 'Chef Mustapha', '0550 44 33 22', '16:00', 'Menu traditionnel (Chorba frik, Couscous royal, Tajine l''ham l''hehlou). 3 camions frigorifiques.', true, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_vendors" ("id", "business_id", "reservation_id", "category", "company_name", "contact_person", "phone", "arrival_time", "special_notes", "is_confirmed", "created_at", "updated_at") VALUES (2, 1, 4, 'dj_orchestre', 'DJ Amine Sound & Light', 'Amine', '0661 88 99 00', '17:00', 'Prévoir branchement console régie salle + étincelles froides entrée.', true, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_vendors" ("id", "business_id", "reservation_id", "category", "company_name", "contact_person", "phone", "arrival_time", "special_notes", "is_confirmed", "created_at", "updated_at") VALUES (3, 1, 4, 'photographe', 'Studio Lumière Mariage', 'Karim & Samira', '0770 12 34 56', '18:00', '2 caméramans + 1 photographe drone terrasse.', true, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_vendors" ("id", "business_id", "reservation_id", "category", "company_name", "contact_person", "phone", "arrival_time", "special_notes", "is_confirmed", "created_at", "updated_at") VALUES (4, 1, 4, 'neguafa', 'Négafa La Princesse d''Oran', 'Madame Meriem', '0555 99 88 77', '17:30', 'Loge mariée VIP requise avec fer à repasser vapeur et grands miroirs éclairés.', true, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_vendors" ("id", "business_id", "reservation_id", "category", "company_name", "contact_person", "phone", "arrival_time", "special_notes", "is_confirmed", "created_at", "updated_at") VALUES (5, 1, 4, 'patisserie', 'Pâtisserie Prestige Gourmet', 'Rachid', '0670 55 44 33', '21:00', 'Livraison pièce montée 5 étages. Stocker dans chambre froide négative.', true, '2026-09-26 19:40:31', '2026-09-26 19:40:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."event_vendors" ("id", "business_id", "reservation_id", "category", "company_name", "contact_person", "phone", "arrival_time", "special_notes", "is_confirmed", "created_at", "updated_at") VALUES (6, 1, 5, 'traiteur', 'traiteur', 'Abdo ka', '654654', '16:30', NULL, true, '2026-09-26 19:58:05', '2026-09-26 19:58:05') ON CONFLICT DO NOTHING;


--
-- TOC entry 5409 (class 0 OID 30354)
-- Dependencies: 256
-- Data for Name: expense_categories; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (1, 1, 'Salaires & Charges', 'الرواتب والأعباء', '#6366f1', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (2, 1, 'Loyer & Charges locatives', 'الإيجار والأعباء', '#8b5cf6', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (3, 1, 'Eau, Gaz & Électricité', 'الماء والغاز والكهرباء', '#0ea5e9', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (4, 1, 'Entretien & Réparations', 'الصيانة والإصلاحات', '#f59e0b', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (5, 1, 'Fournitures & Consommables', 'المستلزمات', '#10b981', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (6, 1, 'Communication & Internet', 'الاتصالات والإنترنت', '#06b6d4', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (7, 1, 'Marketing & Publicité', 'التسويق والإعلان', '#f43f5e', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (8, 1, 'Taxes & Impôts', 'الضرائب والرسوم', '#dc2626', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (9, 1, 'Prestataires externes', 'مزودو الخدمات', '#64748b', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;
INSERT INTO "public"."expense_categories" ("id", "business_id", "name_fr", "name_ar", "color", "is_active", "created_at", "updated_at") VALUES (10, 1, 'Divers', 'متفرقات', '#94a3b8', true, '2026-09-25 20:12:59', '2026-09-25 20:12:59') ON CONFLICT DO NOTHING;


--
-- TOC entry 5411 (class 0 OID 30373)
-- Dependencies: 258
-- Data for Name: expenses; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5386 (class 0 OID 29888)
-- Dependencies: 233
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5413 (class 0 OID 30420)
-- Dependencies: 260
-- Data for Name: inventory_items; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (2, 1, 'Chaises Chiavari dorées avec galette', 'furniture', 'pièce', 420, 350, 50, 'Chaises de mariage haut de gamme finition dorée avec coussin blanc amovible.', 'Hangar Réserve A - Racks 2 & 3', 4500.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (3, 1, 'Fauteuil trône des mariés (Kosha Royale)', 'furniture', 'pièce', 2, 2, 1, 'Grand trône baroque capitonné blanc et or pour estrade des mariés.', 'Estrade / Stock Scène', 120000.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (4, 1, 'Assiettes plates porcelaine liseré or', 'tableware', 'pièce', 500, 450, 80, 'Porcelaine blanche de prestige avec double filet doré.', 'Office traiteur - Caisses V1 à V10', 750.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (5, 1, 'Verres à pied en cristal gravé', 'tableware', 'pièce', 480, 410, 60, 'Verres à eau et jus en cristal pour table d’honneur et invités.', 'Office traiteur - Racks verres', 600.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (6, 1, 'Ménagère complète couverts dorés (Fourchette, Couteau, Cuillère)', 'tableware', 'lot', 450, 400, 50, 'Couverts en acier inoxydable doré brossé.', 'Office traiteur - Tiroirs sécurisés', 1200.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (7, 1, 'Nappes rondes blanches satinées (Ø 300cm)', 'linen', 'pièce', 55, 42, 15, 'Retombée totale au sol, repassage facile, anti-tâche.', 'Lingerie - Armoire 1', 3500.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (8, 1, 'Serviettes de table assorties blanches brodées', 'linen', 'pièce', 500, 430, 70, 'Serviettes 50x50cm coton satiné.', 'Lingerie - Armoire 2', 350.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (9, 1, 'Projecteurs PAR LED RGBW sans fil sur batterie', 'lighting', 'pièce', 24, 18, 6, 'Projecteurs d’ambiance murale contrôlables par DMX/télécommande.', 'Régie technique - Flight cases L1', 14000.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (10, 1, 'Machine à étincelles froides (Cold Sparkler)', 'lighting', 'pièce', 4, 3, 2, 'Effet pyrotechnique intérieur sans danger pour ouverture de bal.', 'Régie technique - Flight case FX', 45000.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (11, 1, 'Paire d’enceintes amplifiées 1000W + Trépieds', 'audio', 'lot', 5, 5, 1, 'Enceintes de sonorisation d’appoint pour terrasse ou salle secondaire.', 'Régie son', 85000.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 20:02:13') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (1, 1, 'Tables rondes 8-10 personnes (Ø 180cm)', 'furniture', 'pièce', 50, 42, 12, 'Mis à jour avec succès', 'Hangar Réserve A - Rack 1 Modifié', 18500.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 20:10:51') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (13, 1, 'Arche florale hexagonale d’honneur avec fleurs artificielles premium', 'decoration', 'pièce', 16, 16, 1, 'Grande arche démontable pour coin photos et entrée des mariés.', 'Stock déco - Zone B', 55000.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 21:50:32') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (12, 1, 'Set de microphones HF sans fil double canal', 'audio', 'lot', 5, 4, 2, 'Micros main pour animateurs et DJ.', 'Régie son - Valise HF', 28000.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 20:20:26') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_items" ("id", "business_id", "name", "category", "unit", "quantity_total", "quantity_available", "quantity_alert", "description", "location", "unit_cost", "is_active", "created_by", "created_at", "updated_at") VALUES (14, 1, 'Centres de table candélabres dorés 5 branches', 'decoration', 'pièce', 41, 41, 8, 'Chandelier 75cm finition or brillant avec coupelles photophores.', 'Stock déco - Rayon C3', 6500.00, true, 2, '2026-09-26 19:27:03', '2026-09-26 21:50:42') ON CONFLICT DO NOTHING;


--
-- TOC entry 5415 (class 0 OID 30453)
-- Dependencies: 262
-- Data for Name: inventory_movements; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (1, 1, 1, NULL, 'IN', 45, 0, 45, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (2, 1, 1, NULL, 'RESERVE', 7, 45, 38, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (3, 1, 2, NULL, 'IN', 420, 0, 420, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (4, 1, 2, NULL, 'RESERVE', 70, 420, 350, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (5, 1, 3, NULL, 'IN', 2, 0, 2, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (6, 1, 4, NULL, 'IN', 500, 0, 500, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (7, 1, 4, NULL, 'RESERVE', 50, 500, 450, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (8, 1, 5, NULL, 'IN', 480, 0, 480, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (9, 1, 5, NULL, 'RESERVE', 70, 480, 410, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (10, 1, 6, NULL, 'IN', 450, 0, 450, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (11, 1, 6, NULL, 'RESERVE', 50, 450, 400, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (12, 1, 7, NULL, 'IN', 55, 0, 55, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (13, 1, 7, NULL, 'RESERVE', 13, 55, 42, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (14, 1, 8, NULL, 'IN', 500, 0, 500, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (15, 1, 8, NULL, 'RESERVE', 70, 500, 430, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (16, 1, 9, NULL, 'IN', 24, 0, 24, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (17, 1, 9, NULL, 'RESERVE', 6, 24, 18, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (18, 1, 10, NULL, 'IN', 4, 0, 4, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (19, 1, 10, NULL, 'RESERVE', 1, 4, 3, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (20, 1, 11, NULL, 'IN', 4, 0, 4, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (21, 1, 11, NULL, 'RESERVE', 1, 4, 3, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (22, 1, 12, NULL, 'IN', 4, 0, 4, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (23, 1, 13, NULL, 'IN', 2, 0, 2, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (24, 1, 13, NULL, 'RESERVE', 1, 2, 1, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (25, 1, 14, NULL, 'IN', 40, 0, 40, 'Inventaire initial et mise en stock', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (26, 1, 14, NULL, 'RESERVE', 5, 40, 35, 'Affectation pour les événements du weekend', NULL, 2, '2026-09-26 19:27:03', '2026-09-26 19:27:03') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (27, 1, 13, NULL, 'IN', 2, 1, 3, NULL, NULL, 1, '2026-09-26 19:52:04', '2026-09-26 19:52:04') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (28, 1, 11, NULL, 'RETURN', 1, 3, 4, NULL, NULL, 1, '2026-09-26 20:01:26', '2026-09-26 20:01:26') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (29, 1, 11, NULL, 'RETURN', 1, 4, 5, NULL, NULL, 1, '2026-09-26 20:01:28', '2026-09-26 20:01:28') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (30, 1, 11, NULL, 'ADJUST', 1, 5, 5, NULL, NULL, 1, '2026-09-26 20:02:13', '2026-09-26 20:02:13') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (31, 1, 13, NULL, 'ADJUST', 1, 3, 4, NULL, NULL, 1, '2026-09-26 20:03:16', '2026-09-26 20:03:16') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (32, 1, 14, NULL, 'ADJUST', 1, 35, 40, NULL, NULL, 1, '2026-09-26 20:03:30', '2026-09-26 20:03:30') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (33, 1, 1, NULL, 'ADJUST', 4, 38, 42, 'Mise à jour directe fiche matériel (Total: 45 → 50, Dispo: 38 → 42)', NULL, 1, '2026-09-26 20:10:51', '2026-09-26 20:10:51') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (34, 1, 12, NULL, 'OUT', 1, 4, 3, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 20:20:18', '2026-09-26 20:20:18') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (35, 1, 12, NULL, 'IN', 1, 3, 4, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 20:20:26', '2026-09-26 20:20:26') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (36, 1, 14, NULL, 'OUT', 1, 40, 39, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:08', '2026-09-26 21:49:08') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (37, 1, 13, NULL, 'IN', 1, 4, 5, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:12', '2026-09-26 21:49:12') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (38, 1, 13, NULL, 'OUT', 1, 5, 4, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:15', '2026-09-26 21:49:15') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (39, 1, 13, NULL, 'OUT', 1, 4, 3, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:18', '2026-09-26 21:49:18') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (40, 1, 13, NULL, 'IN', 1, 3, 4, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:20', '2026-09-26 21:49:20') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (41, 1, 13, NULL, 'IN', 1, 4, 5, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:23', '2026-09-26 21:49:23') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (42, 1, 13, NULL, 'IN', 1, 5, 6, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:28', '2026-09-26 21:49:28') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (43, 1, 13, NULL, 'IN', 1, 6, 7, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:31', '2026-09-26 21:49:31') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (44, 1, 13, NULL, 'IN', 1, 7, 8, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:34', '2026-09-26 21:49:34') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (45, 1, 13, NULL, 'IN', 1, 8, 9, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:36', '2026-09-26 21:49:36') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (46, 1, 13, NULL, 'IN', 1, 9, 10, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:41', '2026-09-26 21:49:41') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (47, 1, 13, NULL, 'OUT', 1, 10, 9, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:44', '2026-09-26 21:49:44') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (48, 1, 13, NULL, 'IN', 1, 9, 10, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:47', '2026-09-26 21:49:47') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (49, 1, 14, NULL, 'IN', 1, 39, 40, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:49:52', '2026-09-26 21:49:52') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (50, 1, 13, NULL, 'IN', 1, 10, 11, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:50:07', '2026-09-26 21:50:07') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (51, 1, 13, NULL, 'IN', 1, 11, 12, 'Ajustement rapide (+/- 1)', NULL, 1, '2026-09-26 21:50:18', '2026-09-26 21:50:18') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (52, 1, 13, NULL, 'ADJUST', 4, 12, 16, 'Mise à jour directe fiche matériel (Total: 15 → 16, Dispo: 12 → 16)', NULL, 1, '2026-09-26 21:50:32', '2026-09-26 21:50:32') ON CONFLICT DO NOTHING;
INSERT INTO "public"."inventory_movements" ("id", "business_id", "inventory_item_id", "reservation_id", "type", "quantity", "quantity_before", "quantity_after", "reason", "notes", "created_by", "created_at", "updated_at") VALUES (53, 1, 14, NULL, 'ADJUST', 1, 40, 41, 'Mise à jour directe fiche matériel (Total: 41 → 41, Dispo: 40 → 41)', NULL, 1, '2026-09-26 21:50:42', '2026-09-26 21:50:42') ON CONFLICT DO NOTHING;


--
-- TOC entry 5384 (class 0 OID 29873)
-- Dependencies: 231
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5383 (class 0 OID 29858)
-- Dependencies: 230
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5373 (class 0 OID 29753)
-- Dependencies: 220
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (1, '0001_01_01_000000_create_businesses_table', 1) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (2, '0001_01_01_000001_create_cache_table', 1) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (3, '0001_01_01_000001_create_users_table', 1) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (4, '0001_01_01_000002_create_jobs_table', 1) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (5, '2026_09_25_000004_create_audit_logs_table', 1) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (6, '2026_09_25_000005_create_halls_and_reservations_tables', 2) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (7, '2026_09_25_000006_create_customers_and_leads_tables', 3) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (9, '2026_09_25_000008_create_payments_and_cash_registers_tables', 5) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (10, '2026_09_25_000009_create_expenses_tables', 6) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (13, '2026_09_26_000012_create_inventory_tables', 9) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (14, '2026_09_26_000013_create_operations_tables', 10) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (16, '2026_09_25_000007_create_packages_and_quotations_tables', 11) ON CONFLICT DO NOTHING;
INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES (17, '2026_09_26_000010_add_calculation_fields_to_quotations_table', 11) ON CONFLICT DO NOTHING;


--
-- TOC entry 5403 (class 0 OID 30182)
-- Dependencies: 250
-- Data for Name: packages; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."packages" ("id", "business_id", "name_fr", "name_ar", "description_fr", "description_ar", "base_price", "price_per_guest", "included_items", "is_active", "created_at", "updated_at") VALUES (1, 1, 'Pack Essentiel', 'الباقة الكلاسيكية', 'Idéal pour fiançailles, khitana ou réceptions intimes.', 'مثالية للخطوبة، الختان أو الحفلات العائلية البسيطة.', 180000.00, 0.00, '["Location de la salle climatis\u00e9e \/ chauff\u00e9e","Tables rondes et chaises avec housses et n\u0153uds","Podium des mari\u00e9s avec tr\u00f4ne classique","\u00c9quipe d''accueil et de s\u00e9curit\u00e9 \u00e0 l''entr\u00e9e","Nettoyage avant et apr\u00e8s \u00e9v\u00e9nement"]', true, '2026-09-25 19:51:38', '2026-09-25 19:51:38') ON CONFLICT DO NOTHING;
INSERT INTO "public"."packages" ("id", "business_id", "name_fr", "name_ar", "description_fr", "description_ar", "base_price", "price_per_guest", "included_items", "is_active", "created_at", "updated_at") VALUES (2, 1, 'Pack Prestige', 'باقة البرستيج', 'Le choix n°1 des mariages algériens modernes.', 'الخيار الأكثر طلباً للأعراس مع هندسة صوتية وإضاءة احترافية.', 280000.00, 0.00, '["Tous les avantages du Pack Essentiel","Sonorisation professionnelle & DJ animateur","Jeux de lumi\u00e8res dynamiques & Machine \u00e0 fum\u00e9e lourde pour l''entr\u00e9e","Tr\u00f4ne royal moderne avec arche florale illumin\u00e9e","Cam\u00e9rawoman d\u00e9di\u00e9e dans la salle des femmes","Salon priv\u00e9 r\u00e9serv\u00e9 pour les retouches de la mari\u00e9e"]', true, '2026-09-25 19:51:38', '2026-09-25 19:51:38') ON CONFLICT DO NOTHING;
INSERT INTO "public"."packages" ("id", "business_id", "name_fr", "name_ar", "description_fr", "description_ar", "base_price", "price_per_guest", "included_items", "is_active", "created_at", "updated_at") VALUES (3, 1, 'Pack Impérial VIP', 'الباقة الملكية الشاملة VIP', 'L''excellence absolue avec service traiteur thé/café et couverture vidéo premium.', 'التميز الفاخر مع خدمة القهوة والشاي والتصوير عالي الدقة وسويت العروسة.', 420000.00, 800.00, '["Tous les avantages du Pack Prestige","Couverture vid\u00e9o multi-cam\u00e9ras 4K & Drone d''int\u00e9rieur","Entr\u00e9e traditionnelle (Zorna \/ Cort\u00e8ge d''honneur)","Service th\u00e9 \u00e0 la menthe & caf\u00e9 traditionnel \u00e0 volont\u00e9","Serveurs et serveuses professionnels en tenue d''apparat (1 pour 30 invit\u00e9s)","Suite VIP grand confort pour les mari\u00e9s avec salle de bain","Table d''honneur personnalis\u00e9e avec fleurs fra\u00eeches"]', true, '2026-09-25 19:51:38', '2026-09-25 19:51:38') ON CONFLICT DO NOTHING;


--
-- TOC entry 5380 (class 0 OID 29836)
-- Dependencies: 227
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5407 (class 0 OID 30312)
-- Dependencies: 254
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5423 (class 0 OID 30591)
-- Dependencies: 270
-- Data for Name: quotations; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--



--
-- TOC entry 5396 (class 0 OID 30010)
-- Dependencies: 243
-- Data for Name: reservations; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."reservations" ("id", "business_id", "reservation_number", "venue_space_id", "time_slot_id", "customer_name", "customer_phone", "bride_name", "groom_name", "event_type", "event_date", "start_time", "end_time", "expected_guests", "status", "payment_status", "hold_expires_at", "total_price", "deposit_required", "deposit_paid", "total_paid", "remaining_balance", "internal_notes", "created_by", "created_at", "updated_at", "customer_id", "cancelled_at", "cancellation_reason", "cancelled_by", "refund_amount", "package_id") VALUES (4, 1, 'RES-2026-0001', 1, 1, 'sdsd', '2132132', 'dddd', 'ddd', 'Mariage', '2026-09-26', '16:00:00', '23:30:00', 1000, 'CANCELLED', 'UNPAID', NULL, 250000.00, 50000.00, 0.00, 0.00, 250000.00, NULL, 1, '2026-09-26 01:06:17', '2026-09-26 01:06:46', 1, '2026-09-26 01:06:46', 'sdsdcsd', 1, 0.00, NULL) ON CONFLICT DO NOTHING;
INSERT INTO "public"."reservations" ("id", "business_id", "reservation_number", "venue_space_id", "time_slot_id", "customer_name", "customer_phone", "bride_name", "groom_name", "event_type", "event_date", "start_time", "end_time", "expected_guests", "status", "payment_status", "hold_expires_at", "total_price", "deposit_required", "deposit_paid", "total_paid", "remaining_balance", "internal_notes", "created_by", "created_at", "updated_at", "customer_id", "cancelled_at", "cancellation_reason", "cancelled_by", "refund_amount", "package_id") VALUES (5, 1, 'RES-2026-0002', 1, 3, 'jhgjhg', '654665', 'jhjh', 'kk', 'Mariage', '2026-09-26', '10:00:00', '02:00:00', 200, 'CANCELLED', 'UNPAID', NULL, 250000.00, 50000.00, 0.00, 0.00, 250000.00, 'kjhgjhg', 1, '2026-09-26 18:11:31', '2026-09-26 19:59:40', 2, '2026-09-26 19:59:40', 'mlm.,m.,m', 1, 0.00, NULL) ON CONFLICT DO NOTHING;


--
-- TOC entry 5381 (class 0 OID 29845)
-- Dependencies: 228
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."sessions" ("id", "user_id", "ip_address", "user_agent", "payload", "last_activity") VALUES ('xVNxszAI4ecSvmN24xsOKGKEcW75tItIdHUz1Cz5', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJQSXR4cHl4YURITE5vNjVRcU9idVZiUlZUaVVtZk5HY0ljWDJnVmVHIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2ludmVudG9yeT9jYXRlZ29yeT1kZWNvcmF0aW9uIiwicm91dGUiOiJpbnZlbnRvcnkuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiOTY5ODY2ZWEyN2FhNGRhMmM1NWU3MDhjZTFhYzIyNmU0ZDA4MzdjY2I4MWFiYWFiZjY3NjE3MDBjYzAwMDA0MCIsImxvY2FsZSI6ImZyIn0=', 1790459442) ON CONFLICT DO NOTHING;
INSERT INTO "public"."sessions" ("id", "user_id", "ip_address", "user_agent", "payload", "last_activity") VALUES ('y471rzA9Hz6DMgeBQay0iodHDxJPGXRYVqbGRy08', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJPc0NXSk5TOGMyY0o0NFZnMUZpcEo5V21NSlp4NWMwd1QyR0hpb2NEIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9xdW90YXRpb25zXC9jcmVhdGUiLCJyb3V0ZSI6InF1b3RhdGlvbnMuY3JlYXRlIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiOTY5ODY2ZWEyN2FhNGRhMmM1NWU3MDhjZTFhYzIyNmU0ZDA4MzdjY2I4MWFiYWFiZjY3NjE3MDBjYzAwMDA0MCIsImxvY2FsZSI6ImZyIn0=', 1790464708) ON CONFLICT DO NOTHING;


--
-- TOC entry 5394 (class 0 OID 29986)
-- Dependencies: 241
-- Data for Name: time_slots; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."time_slots" ("id", "business_id", "name_fr", "name_ar", "start_time", "end_time", "is_overnight", "sort_order", "is_active", "created_at", "updated_at") VALUES (1, 1, 'Après-midi', 'فترة الظهيرة والمساء', '14:00:00', '18:00:00', false, 1, true, '2026-09-25 19:09:25', '2026-09-25 19:09:25') ON CONFLICT DO NOTHING;
INSERT INTO "public"."time_slots" ("id", "business_id", "name_fr", "name_ar", "start_time", "end_time", "is_overnight", "sort_order", "is_active", "created_at", "updated_at") VALUES (2, 1, 'Soirée', 'سهرة العرس', '19:00:00', '02:00:00', true, 2, true, '2026-09-25 19:09:25', '2026-09-25 19:09:25') ON CONFLICT DO NOTHING;
INSERT INTO "public"."time_slots" ("id", "business_id", "name_fr", "name_ar", "start_time", "end_time", "is_overnight", "sort_order", "is_active", "created_at", "updated_at") VALUES (3, 1, 'Journée Entière', 'يوم كامل', '10:00:00', '02:00:00', true, 3, true, '2026-09-25 19:09:25', '2026-09-25 19:09:25') ON CONFLICT DO NOTHING;


--
-- TOC entry 5379 (class 0 OID 29811)
-- Dependencies: 226
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."users" ("id", "business_id", "name", "email", "phone", "role", "preferred_language", "is_active", "email_verified_at", "password", "remember_token", "created_at", "updated_at") VALUES (2, 1, 'Sara Mansouri', 'manager@palaiselbahia.dz', '0661 44 55 66', 'MANAGER', 'fr', true, NULL, '$2y$12$4XPfBWlsFtvrlhi4gh2zhe2UDhdFRIqgqdgmjbhxpf7l48ZcpONDi', NULL, '2026-09-25 18:54:24', '2026-09-25 19:09:24') ON CONFLICT DO NOTHING;
INSERT INTO "public"."users" ("id", "business_id", "name", "email", "phone", "role", "preferred_language", "is_active", "email_verified_at", "password", "remember_token", "created_at", "updated_at") VALUES (4, 1, 'Ahmed Touati', 'compta@palaiselbahia.dz', '0560 33 44 55', 'ACCOUNTANT', 'fr', true, NULL, '$2y$12$Eh4wqPULaE8ZLHBZ8kOM6.Hr6ANzk1iEECuUpyC2v9ZLDbcn3KGWa', NULL, '2026-09-25 18:54:25', '2026-09-25 19:09:25') ON CONFLICT DO NOTHING;
INSERT INTO "public"."users" ("id", "business_id", "name", "email", "phone", "role", "preferred_language", "is_active", "email_verified_at", "password", "remember_token", "created_at", "updated_at") VALUES (3, 1, 'Yasmine Belkacem', 'reception@palaiselbahia.dz', '0770 77 88 99', 'RECEPTIONIST', 'fr', true, NULL, '$2y$12$sH0OgYwZOryedj/xTR5USuWcdJosUAg4q7y0dfapSGwIuSAWvBUk.', NULL, '2026-09-25 18:54:24', '2026-09-26 00:33:52') ON CONFLICT DO NOTHING;
INSERT INTO "public"."users" ("id", "business_id", "name", "email", "phone", "role", "preferred_language", "is_active", "email_verified_at", "password", "remember_token", "created_at", "updated_at") VALUES (5, 1, 'Amine Khelifi', 'events@palaiselbahia.dz', '0671 22 33 44', 'EVENT_COORDINATOR', 'fr', true, NULL, '$2y$12$.GU.hf.Rmg1ngPDRhdVX0.TkiusndPf339hbxdO5unU864QxFI1vy', NULL, '2026-09-25 18:54:25', '2026-09-26 00:41:39') ON CONFLICT DO NOTHING;
INSERT INTO "public"."users" ("id", "business_id", "name", "email", "phone", "role", "preferred_language", "is_active", "email_verified_at", "password", "remember_token", "created_at", "updated_at") VALUES (1, 1, 'Kamel Benali', 'owner@palaiselbahia.dz', '0550 11 22 33', 'OWNER', 'fr', true, NULL, '$2y$12$DoxLWkpUiJ2vTeoZgeQUI..gXs9cchWyWMScUhi1CI7BZd/oaUf2K', NULL, '2026-09-25 18:54:24', '2026-09-26 19:34:39') ON CONFLICT DO NOTHING;


--
-- TOC entry 5392 (class 0 OID 29956)
-- Dependencies: 239
-- Data for Name: venue_spaces; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."venue_spaces" ("id", "business_id", "venue_id", "name_fr", "name_ar", "capacity_min", "capacity_max", "base_price", "is_active", "created_at", "updated_at") VALUES (1, 1, 1, 'Salle Royale', 'القاعة الملكية', 150, 450, 250000.00, true, '2026-09-25 19:09:25', '2026-09-25 19:09:25') ON CONFLICT DO NOTHING;


--
-- TOC entry 5390 (class 0 OID 29936)
-- Dependencies: 237
-- Data for Name: venues; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."venues" ("id", "business_id", "name_fr", "name_ar", "address", "is_active", "created_at", "updated_at") VALUES (1, 1, 'Palais El Bahia - Alger', 'قصر الباهية - الجزائر العاصمة', '14 Boulevard des Martyrs, Alger', true, '2026-09-25 19:09:25', '2026-09-25 19:09:25') ON CONFLICT DO NOTHING;


--
-- TOC entry 5397 (class 0 OID 30069)
-- Dependencies: 244
-- Data for Name: wilayas; Type: TABLE DATA; Schema: public; Owner: ITAdmin
--

INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (1, '01', 'Adrar', 'أدرار') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (2, '02', 'Chlef', 'الشلف') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (3, '03', 'Laghouat', 'الأغواط') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (4, '04', 'Oum El Bouaghi', 'أم البواقي') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (5, '05', 'Batna', 'باتنة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (6, '06', 'Béjaïa', 'بجاية') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (7, '07', 'Biskra', 'بسكرة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (8, '08', 'Béchar', 'بشار') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (9, '09', 'Blida', 'البليدة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (10, '10', 'Bouira', 'البويرة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (11, '11', 'Tamanrasset', 'تمنراست') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (12, '12', 'Tébessa', 'تبسة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (13, '13', 'Tlemcen', 'تلمسان') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (14, '14', 'Tiaret', 'تيارت') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (15, '15', 'Tizi Ouzou', 'تيزي وزو') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (16, '16', 'Alger', 'الجزائر') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (17, '17', 'Djelfa', 'الجلفة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (18, '18', 'Jijel', 'جيجل') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (19, '19', 'Sétif', 'سطيف') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (20, '20', 'Saïda', 'سعيدة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (21, '21', 'Skikda', 'سكيكدة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (22, '22', 'Sidi Bel Abbès', 'سيدي بلعباس') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (23, '23', 'Annaba', 'عنابة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (24, '24', 'Guelma', 'قالمة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (25, '25', 'Constantine', 'قسنطينة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (26, '26', 'Médéa', 'المدية') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (27, '27', 'Mostaganem', 'مستغانم') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (28, '28', 'M''Sila', 'المسيلة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (29, '29', 'Mascara', 'معسكر') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (30, '30', 'Ouargla', 'ورقلة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (31, '31', 'Oran', 'وهران') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (32, '32', 'El Bayadh', 'البيض') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (33, '33', 'Illizi', 'إليزي') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (34, '34', 'Bordj Bou Arréridj', 'برج بوعريريج') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (35, '35', 'Boumerdès', 'بومرداس') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (36, '36', 'El Tarf', 'الطارف') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (37, '37', 'Tindouf', 'تندوف') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (38, '38', 'Tissemsilt', 'تيسمسيلت') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (39, '39', 'El Oued', 'الوادي') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (40, '40', 'Khenchela', 'خنشلة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (41, '41', 'Souk Ahras', 'سوق أهراس') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (42, '42', 'Tipaza', 'تيبازة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (43, '43', 'Mila', 'ميلة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (44, '44', 'Aïn Defla', 'عين الدفلى') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (45, '45', 'Naâma', 'النعامة') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (46, '46', 'Aïn Témouchent', 'عين تموشنت') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (47, '47', 'Ghardaïa', 'غرداية') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (48, '48', 'Relizane', 'غليزان') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (49, '49', 'Timimoun', 'تيميمون') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (50, '50', 'Bordj Badji Mokhtar', 'برج باجي مختار') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (51, '51', 'Ouled Djellal', 'أولاد جلال') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (52, '52', 'Béni Abbès', 'بني عباس') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (53, '53', 'In Salah', 'عين صالح') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (54, '54', 'In Guezzam', 'عين قزام') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (55, '55', 'Touggourt', 'تقرت') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (56, '56', 'Djanet', 'جانت') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (57, '57', 'El M''Ghair', 'المغير') ON CONFLICT DO NOTHING;
INSERT INTO "public"."wilayas" ("id", "code", "name_fr", "name_ar") VALUES (58, '58', 'El Meniaa', 'المنيعة') ON CONFLICT DO NOTHING;


--
-- TOC entry 5454 (class 0 OID 0)
-- Dependencies: 234
-- Name: audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."audit_logs_id_seq"', 29, true);


--
-- TOC entry 5455 (class 0 OID 0)
-- Dependencies: 221
-- Name: businesses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."businesses_id_seq"', 1, true);


--
-- TOC entry 5456 (class 0 OID 0)
-- Dependencies: 251
-- Name: cash_registers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."cash_registers_id_seq"', 1, true);


--
-- TOC entry 5457 (class 0 OID 0)
-- Dependencies: 245
-- Name: communes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."communes_id_seq"', 35, true);


--
-- TOC entry 5458 (class 0 OID 0)
-- Dependencies: 247
-- Name: customers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."customers_id_seq"', 2, true);


--
-- TOC entry 5459 (class 0 OID 0)
-- Dependencies: 263
-- Name: event_tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."event_tasks_id_seq"', 30, true);


--
-- TOC entry 5460 (class 0 OID 0)
-- Dependencies: 267
-- Name: event_timelines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."event_timelines_id_seq"', 14, true);


--
-- TOC entry 5461 (class 0 OID 0)
-- Dependencies: 265
-- Name: event_vendors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."event_vendors_id_seq"', 6, true);


--
-- TOC entry 5462 (class 0 OID 0)
-- Dependencies: 255
-- Name: expense_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."expense_categories_id_seq"', 10, true);


--
-- TOC entry 5463 (class 0 OID 0)
-- Dependencies: 257
-- Name: expenses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."expenses_id_seq"', 1, false);


--
-- TOC entry 5464 (class 0 OID 0)
-- Dependencies: 232
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."failed_jobs_id_seq"', 1, false);


--
-- TOC entry 5465 (class 0 OID 0)
-- Dependencies: 259
-- Name: inventory_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."inventory_items_id_seq"', 14, true);


--
-- TOC entry 5466 (class 0 OID 0)
-- Dependencies: 261
-- Name: inventory_movements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."inventory_movements_id_seq"', 53, true);


--
-- TOC entry 5467 (class 0 OID 0)
-- Dependencies: 229
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."jobs_id_seq"', 1, false);


--
-- TOC entry 5468 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."migrations_id_seq"', 17, true);


--
-- TOC entry 5469 (class 0 OID 0)
-- Dependencies: 249
-- Name: packages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."packages_id_seq"', 3, true);


--
-- TOC entry 5470 (class 0 OID 0)
-- Dependencies: 253
-- Name: payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."payments_id_seq"', 1, false);


--
-- TOC entry 5471 (class 0 OID 0)
-- Dependencies: 269
-- Name: quotations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."quotations_id_seq"', 1, false);


--
-- TOC entry 5472 (class 0 OID 0)
-- Dependencies: 242
-- Name: reservations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."reservations_id_seq"', 5, true);


--
-- TOC entry 5473 (class 0 OID 0)
-- Dependencies: 240
-- Name: time_slots_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."time_slots_id_seq"', 3, true);


--
-- TOC entry 5474 (class 0 OID 0)
-- Dependencies: 225
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."users_id_seq"', 5, true);


--
-- TOC entry 5475 (class 0 OID 0)
-- Dependencies: 238
-- Name: venue_spaces_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."venue_spaces_id_seq"', 3, true);


--
-- TOC entry 5476 (class 0 OID 0)
-- Dependencies: 236
-- Name: venues_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ITAdmin
--

SELECT pg_catalog.setval('"public"."venues_id_seq"', 1, true);


--
-- TOC entry 5112 (class 2606 OID 29922)
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."audit_logs"
    ADD CONSTRAINT "audit_logs_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5080 (class 2606 OID 29785)
-- Name: businesses businesses_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."businesses"
    ADD CONSTRAINT "businesses_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5082 (class 2606 OID 29787)
-- Name: businesses businesses_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."businesses"
    ADD CONSTRAINT "businesses_slug_unique" UNIQUE ("slug");


--
-- TOC entry 5088 (class 2606 OID 29808)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cache_locks"
    ADD CONSTRAINT "cache_locks_pkey" PRIMARY KEY ("key");


--
-- TOC entry 5085 (class 2606 OID 29797)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cache"
    ADD CONSTRAINT "cache_pkey" PRIMARY KEY ("key");


--
-- TOC entry 5142 (class 2606 OID 30294)
-- Name: cash_registers cash_registers_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cash_registers"
    ADD CONSTRAINT "cash_registers_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5129 (class 2606 OID 30090)
-- Name: communes communes_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."communes"
    ADD CONSTRAINT "communes_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5132 (class 2606 OID 30131)
-- Name: customers customers_business_id_customer_code_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."customers"
    ADD CONSTRAINT "customers_business_id_customer_code_unique" UNIQUE ("business_id", "customer_code");


--
-- TOC entry 5136 (class 2606 OID 30114)
-- Name: customers customers_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."customers"
    ADD CONSTRAINT "customers_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5161 (class 2606 OID 30510)
-- Name: event_tasks event_tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_tasks"
    ADD CONSTRAINT "event_tasks_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5168 (class 2606 OID 30578)
-- Name: event_timelines event_timelines_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_timelines"
    ADD CONSTRAINT "event_timelines_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5165 (class 2606 OID 30549)
-- Name: event_vendors event_vendors_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_vendors"
    ADD CONSTRAINT "event_vendors_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5150 (class 2606 OID 30366)
-- Name: expense_categories expense_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expense_categories"
    ADD CONSTRAINT "expense_categories_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5154 (class 2606 OID 30388)
-- Name: expenses expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expenses"
    ADD CONSTRAINT "expenses_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5106 (class 2606 OID 29903)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."failed_jobs"
    ADD CONSTRAINT "failed_jobs_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5108 (class 2606 OID 29906)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."failed_jobs"
    ADD CONSTRAINT "failed_jobs_uuid_unique" UNIQUE ("uuid");


--
-- TOC entry 5156 (class 2606 OID 30441)
-- Name: inventory_items inventory_items_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_items"
    ADD CONSTRAINT "inventory_items_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5158 (class 2606 OID 30467)
-- Name: inventory_movements inventory_movements_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_movements"
    ADD CONSTRAINT "inventory_movements_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5103 (class 2606 OID 29886)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."job_batches"
    ADD CONSTRAINT "job_batches_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5100 (class 2606 OID 29871)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."jobs"
    ADD CONSTRAINT "jobs_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5078 (class 2606 OID 29761)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."migrations"
    ADD CONSTRAINT "migrations_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5139 (class 2606 OID 30198)
-- Name: packages packages_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."packages"
    ADD CONSTRAINT "packages_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5094 (class 2606 OID 29844)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."password_reset_tokens"
    ADD CONSTRAINT "password_reset_tokens_pkey" PRIMARY KEY ("email");


--
-- TOC entry 5145 (class 2606 OID 30328)
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments"
    ADD CONSTRAINT "payments_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5147 (class 2606 OID 30352)
-- Name: payments payments_receipt_number_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments"
    ADD CONSTRAINT "payments_receipt_number_unique" UNIQUE ("receipt_number");


--
-- TOC entry 5172 (class 2606 OID 30616)
-- Name: quotations quotations_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5174 (class 2606 OID 30650)
-- Name: quotations quotations_quote_number_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_quote_number_unique" UNIQUE ("quote_number");


--
-- TOC entry 5122 (class 2606 OID 30044)
-- Name: reservations reservations_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5097 (class 2606 OID 29854)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."sessions"
    ADD CONSTRAINT "sessions_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5118 (class 2606 OID 30003)
-- Name: time_slots time_slots_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."time_slots"
    ADD CONSTRAINT "time_slots_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5090 (class 2606 OID 29835)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."users"
    ADD CONSTRAINT "users_email_unique" UNIQUE ("email");


--
-- TOC entry 5092 (class 2606 OID 29828)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."users"
    ADD CONSTRAINT "users_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5116 (class 2606 OID 29974)
-- Name: venue_spaces venue_spaces_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venue_spaces"
    ADD CONSTRAINT "venue_spaces_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5114 (class 2606 OID 29949)
-- Name: venues venues_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venues"
    ADD CONSTRAINT "venues_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5125 (class 2606 OID 30079)
-- Name: wilayas wilayas_code_unique; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."wilayas"
    ADD CONSTRAINT "wilayas_code_unique" UNIQUE ("code");


--
-- TOC entry 5127 (class 2606 OID 30077)
-- Name: wilayas wilayas_pkey; Type: CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."wilayas"
    ADD CONSTRAINT "wilayas_pkey" PRIMARY KEY ("id");


--
-- TOC entry 5109 (class 1259 OID 29934)
-- Name: audit_logs_auditable_type_auditable_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "audit_logs_auditable_type_auditable_id_index" ON "public"."audit_logs" USING "btree" ("auditable_type", "auditable_id");


--
-- TOC entry 5110 (class 1259 OID 29933)
-- Name: audit_logs_business_id_created_at_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "audit_logs_business_id_created_at_index" ON "public"."audit_logs" USING "btree" ("business_id", "created_at");


--
-- TOC entry 5083 (class 1259 OID 29798)
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "cache_expiration_index" ON "public"."cache" USING "btree" ("expiration");


--
-- TOC entry 5086 (class 1259 OID 29809)
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "cache_locks_expiration_index" ON "public"."cache_locks" USING "btree" ("expiration");


--
-- TOC entry 5140 (class 1259 OID 30310)
-- Name: cash_registers_business_id_register_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "cash_registers_business_id_register_date_index" ON "public"."cash_registers" USING "btree" ("business_id", "register_date");


--
-- TOC entry 5130 (class 1259 OID 30096)
-- Name: communes_wilaya_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "communes_wilaya_id_index" ON "public"."communes" USING "btree" ("wilaya_id");


--
-- TOC entry 5133 (class 1259 OID 30133)
-- Name: customers_business_id_full_name_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "customers_business_id_full_name_index" ON "public"."customers" USING "btree" ("business_id", "full_name");


--
-- TOC entry 5134 (class 1259 OID 30132)
-- Name: customers_business_id_phone_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "customers_business_id_phone_index" ON "public"."customers" USING "btree" ("business_id", "phone");


--
-- TOC entry 5159 (class 1259 OID 30531)
-- Name: event_tasks_business_id_reservation_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "event_tasks_business_id_reservation_id_index" ON "public"."event_tasks" USING "btree" ("business_id", "reservation_id");


--
-- TOC entry 5162 (class 1259 OID 30532)
-- Name: event_tasks_reservation_id_status_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "event_tasks_reservation_id_status_index" ON "public"."event_tasks" USING "btree" ("reservation_id", "status");


--
-- TOC entry 5166 (class 1259 OID 30589)
-- Name: event_timelines_business_id_reservation_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "event_timelines_business_id_reservation_id_index" ON "public"."event_timelines" USING "btree" ("business_id", "reservation_id");


--
-- TOC entry 5163 (class 1259 OID 30560)
-- Name: event_vendors_business_id_reservation_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "event_vendors_business_id_reservation_id_index" ON "public"."event_vendors" USING "btree" ("business_id", "reservation_id");


--
-- TOC entry 5151 (class 1259 OID 30405)
-- Name: expenses_business_id_expense_category_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "expenses_business_id_expense_category_id_index" ON "public"."expenses" USING "btree" ("business_id", "expense_category_id");


--
-- TOC entry 5152 (class 1259 OID 30404)
-- Name: expenses_business_id_expense_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "expenses_business_id_expense_date_index" ON "public"."expenses" USING "btree" ("business_id", "expense_date");


--
-- TOC entry 5104 (class 1259 OID 29904)
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "failed_jobs_connection_queue_failed_at_index" ON "public"."failed_jobs" USING "btree" ("connection", "queue", "failed_at");


--
-- TOC entry 5101 (class 1259 OID 29872)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "jobs_queue_index" ON "public"."jobs" USING "btree" ("queue");


--
-- TOC entry 5137 (class 1259 OID 30204)
-- Name: packages_business_id_is_active_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "packages_business_id_is_active_index" ON "public"."packages" USING "btree" ("business_id", "is_active");


--
-- TOC entry 5143 (class 1259 OID 30349)
-- Name: payments_business_id_payment_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "payments_business_id_payment_date_index" ON "public"."payments" USING "btree" ("business_id", "payment_date");


--
-- TOC entry 5148 (class 1259 OID 30350)
-- Name: payments_reservation_id_payment_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "payments_reservation_id_payment_date_index" ON "public"."payments" USING "btree" ("reservation_id", "payment_date");


--
-- TOC entry 5169 (class 1259 OID 30648)
-- Name: quotations_business_id_event_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "quotations_business_id_event_date_index" ON "public"."quotations" USING "btree" ("business_id", "event_date");


--
-- TOC entry 5170 (class 1259 OID 30647)
-- Name: quotations_business_id_status_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "quotations_business_id_status_index" ON "public"."quotations" USING "btree" ("business_id", "status");


--
-- TOC entry 5119 (class 1259 OID 30066)
-- Name: reservations_business_id_event_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "reservations_business_id_event_date_index" ON "public"."reservations" USING "btree" ("business_id", "event_date");


--
-- TOC entry 5120 (class 1259 OID 30067)
-- Name: reservations_business_id_status_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "reservations_business_id_status_index" ON "public"."reservations" USING "btree" ("business_id", "status");


--
-- TOC entry 5123 (class 1259 OID 30065)
-- Name: reservations_venue_space_id_event_date_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "reservations_venue_space_id_event_date_index" ON "public"."reservations" USING "btree" ("venue_space_id", "event_date");


--
-- TOC entry 5095 (class 1259 OID 29856)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "sessions_last_activity_index" ON "public"."sessions" USING "btree" ("last_activity");


--
-- TOC entry 5098 (class 1259 OID 29855)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: ITAdmin
--

CREATE INDEX "sessions_user_id_index" ON "public"."sessions" USING "btree" ("user_id");


--
-- TOC entry 5176 (class 2606 OID 29923)
-- Name: audit_logs audit_logs_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."audit_logs"
    ADD CONSTRAINT "audit_logs_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5177 (class 2606 OID 29928)
-- Name: audit_logs audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."audit_logs"
    ADD CONSTRAINT "audit_logs_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5194 (class 2606 OID 30295)
-- Name: cash_registers cash_registers_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cash_registers"
    ADD CONSTRAINT "cash_registers_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5195 (class 2606 OID 30305)
-- Name: cash_registers cash_registers_closed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cash_registers"
    ADD CONSTRAINT "cash_registers_closed_by_foreign" FOREIGN KEY ("closed_by") REFERENCES "public"."users"("id");


--
-- TOC entry 5196 (class 2606 OID 30300)
-- Name: cash_registers cash_registers_opened_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."cash_registers"
    ADD CONSTRAINT "cash_registers_opened_by_foreign" FOREIGN KEY ("opened_by") REFERENCES "public"."users"("id");


--
-- TOC entry 5189 (class 2606 OID 30091)
-- Name: communes communes_wilaya_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."communes"
    ADD CONSTRAINT "communes_wilaya_id_foreign" FOREIGN KEY ("wilaya_id") REFERENCES "public"."wilayas"("id") ON DELETE CASCADE;


--
-- TOC entry 5190 (class 2606 OID 30115)
-- Name: customers customers_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."customers"
    ADD CONSTRAINT "customers_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5191 (class 2606 OID 30125)
-- Name: customers customers_commune_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."customers"
    ADD CONSTRAINT "customers_commune_id_foreign" FOREIGN KEY ("commune_id") REFERENCES "public"."communes"("id") ON DELETE SET NULL;


--
-- TOC entry 5192 (class 2606 OID 30120)
-- Name: customers customers_wilaya_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."customers"
    ADD CONSTRAINT "customers_wilaya_id_foreign" FOREIGN KEY ("wilaya_id") REFERENCES "public"."wilayas"("id") ON DELETE SET NULL;


--
-- TOC entry 5211 (class 2606 OID 30521)
-- Name: event_tasks event_tasks_assigned_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_tasks"
    ADD CONSTRAINT "event_tasks_assigned_to_foreign" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5212 (class 2606 OID 30511)
-- Name: event_tasks event_tasks_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_tasks"
    ADD CONSTRAINT "event_tasks_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5213 (class 2606 OID 30526)
-- Name: event_tasks event_tasks_completed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_tasks"
    ADD CONSTRAINT "event_tasks_completed_by_foreign" FOREIGN KEY ("completed_by") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5214 (class 2606 OID 30516)
-- Name: event_tasks event_tasks_reservation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_tasks"
    ADD CONSTRAINT "event_tasks_reservation_id_foreign" FOREIGN KEY ("reservation_id") REFERENCES "public"."reservations"("id") ON DELETE CASCADE;


--
-- TOC entry 5217 (class 2606 OID 30579)
-- Name: event_timelines event_timelines_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_timelines"
    ADD CONSTRAINT "event_timelines_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5218 (class 2606 OID 30584)
-- Name: event_timelines event_timelines_reservation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_timelines"
    ADD CONSTRAINT "event_timelines_reservation_id_foreign" FOREIGN KEY ("reservation_id") REFERENCES "public"."reservations"("id") ON DELETE CASCADE;


--
-- TOC entry 5215 (class 2606 OID 30550)
-- Name: event_vendors event_vendors_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_vendors"
    ADD CONSTRAINT "event_vendors_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5216 (class 2606 OID 30555)
-- Name: event_vendors event_vendors_reservation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."event_vendors"
    ADD CONSTRAINT "event_vendors_reservation_id_foreign" FOREIGN KEY ("reservation_id") REFERENCES "public"."reservations"("id") ON DELETE CASCADE;


--
-- TOC entry 5201 (class 2606 OID 30367)
-- Name: expense_categories expense_categories_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expense_categories"
    ADD CONSTRAINT "expense_categories_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5202 (class 2606 OID 30389)
-- Name: expenses expenses_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expenses"
    ADD CONSTRAINT "expenses_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5203 (class 2606 OID 30399)
-- Name: expenses expenses_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expenses"
    ADD CONSTRAINT "expenses_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE RESTRICT;


--
-- TOC entry 5204 (class 2606 OID 30394)
-- Name: expenses expenses_expense_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."expenses"
    ADD CONSTRAINT "expenses_expense_category_id_foreign" FOREIGN KEY ("expense_category_id") REFERENCES "public"."expense_categories"("id") ON DELETE SET NULL;


--
-- TOC entry 5205 (class 2606 OID 30442)
-- Name: inventory_items inventory_items_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_items"
    ADD CONSTRAINT "inventory_items_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5206 (class 2606 OID 30447)
-- Name: inventory_items inventory_items_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_items"
    ADD CONSTRAINT "inventory_items_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5207 (class 2606 OID 30468)
-- Name: inventory_movements inventory_movements_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_movements"
    ADD CONSTRAINT "inventory_movements_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5208 (class 2606 OID 30483)
-- Name: inventory_movements inventory_movements_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_movements"
    ADD CONSTRAINT "inventory_movements_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5209 (class 2606 OID 30473)
-- Name: inventory_movements inventory_movements_inventory_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_movements"
    ADD CONSTRAINT "inventory_movements_inventory_item_id_foreign" FOREIGN KEY ("inventory_item_id") REFERENCES "public"."inventory_items"("id") ON DELETE CASCADE;


--
-- TOC entry 5210 (class 2606 OID 30478)
-- Name: inventory_movements inventory_movements_reservation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."inventory_movements"
    ADD CONSTRAINT "inventory_movements_reservation_id_foreign" FOREIGN KEY ("reservation_id") REFERENCES "public"."reservations"("id") ON DELETE SET NULL;


--
-- TOC entry 5193 (class 2606 OID 30199)
-- Name: packages packages_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."packages"
    ADD CONSTRAINT "packages_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5197 (class 2606 OID 30329)
-- Name: payments payments_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments"
    ADD CONSTRAINT "payments_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5198 (class 2606 OID 30339)
-- Name: payments payments_cash_register_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments"
    ADD CONSTRAINT "payments_cash_register_id_foreign" FOREIGN KEY ("cash_register_id") REFERENCES "public"."cash_registers"("id") ON DELETE SET NULL;


--
-- TOC entry 5199 (class 2606 OID 30344)
-- Name: payments payments_received_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments"
    ADD CONSTRAINT "payments_received_by_foreign" FOREIGN KEY ("received_by") REFERENCES "public"."users"("id");


--
-- TOC entry 5200 (class 2606 OID 30334)
-- Name: payments payments_reservation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."payments"
    ADD CONSTRAINT "payments_reservation_id_foreign" FOREIGN KEY ("reservation_id") REFERENCES "public"."reservations"("id") ON DELETE CASCADE;


--
-- TOC entry 5219 (class 2606 OID 30617)
-- Name: quotations quotations_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5220 (class 2606 OID 30637)
-- Name: quotations quotations_converted_reservation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_converted_reservation_id_foreign" FOREIGN KEY ("converted_reservation_id") REFERENCES "public"."reservations"("id") ON DELETE SET NULL;


--
-- TOC entry 5221 (class 2606 OID 30642)
-- Name: quotations quotations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5222 (class 2606 OID 30622)
-- Name: quotations quotations_customer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_customer_id_foreign" FOREIGN KEY ("customer_id") REFERENCES "public"."customers"("id") ON DELETE SET NULL;


--
-- TOC entry 5223 (class 2606 OID 30632)
-- Name: quotations quotations_package_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_package_id_foreign" FOREIGN KEY ("package_id") REFERENCES "public"."packages"("id") ON DELETE SET NULL;


--
-- TOC entry 5224 (class 2606 OID 30627)
-- Name: quotations quotations_venue_space_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."quotations"
    ADD CONSTRAINT "quotations_venue_space_id_foreign" FOREIGN KEY ("venue_space_id") REFERENCES "public"."venue_spaces"("id") ON DELETE CASCADE;


--
-- TOC entry 5182 (class 2606 OID 30045)
-- Name: reservations reservations_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5183 (class 2606 OID 30174)
-- Name: reservations reservations_cancelled_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_cancelled_by_foreign" FOREIGN KEY ("cancelled_by") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5184 (class 2606 OID 30060)
-- Name: reservations reservations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE SET NULL;


--
-- TOC entry 5185 (class 2606 OID 30169)
-- Name: reservations reservations_customer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_customer_id_foreign" FOREIGN KEY ("customer_id") REFERENCES "public"."customers"("id") ON DELETE RESTRICT;


--
-- TOC entry 5186 (class 2606 OID 30266)
-- Name: reservations reservations_package_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_package_id_foreign" FOREIGN KEY ("package_id") REFERENCES "public"."packages"("id") ON DELETE SET NULL;


--
-- TOC entry 5187 (class 2606 OID 30055)
-- Name: reservations reservations_time_slot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_time_slot_id_foreign" FOREIGN KEY ("time_slot_id") REFERENCES "public"."time_slots"("id") ON DELETE SET NULL;


--
-- TOC entry 5188 (class 2606 OID 30050)
-- Name: reservations reservations_venue_space_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."reservations"
    ADD CONSTRAINT "reservations_venue_space_id_foreign" FOREIGN KEY ("venue_space_id") REFERENCES "public"."venue_spaces"("id") ON DELETE RESTRICT;


--
-- TOC entry 5181 (class 2606 OID 30004)
-- Name: time_slots time_slots_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."time_slots"
    ADD CONSTRAINT "time_slots_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5175 (class 2606 OID 29829)
-- Name: users users_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."users"
    ADD CONSTRAINT "users_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5179 (class 2606 OID 29975)
-- Name: venue_spaces venue_spaces_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venue_spaces"
    ADD CONSTRAINT "venue_spaces_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


--
-- TOC entry 5180 (class 2606 OID 29980)
-- Name: venue_spaces venue_spaces_venue_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venue_spaces"
    ADD CONSTRAINT "venue_spaces_venue_id_foreign" FOREIGN KEY ("venue_id") REFERENCES "public"."venues"("id") ON DELETE CASCADE;


--
-- TOC entry 5178 (class 2606 OID 29950)
-- Name: venues venues_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ITAdmin
--

ALTER TABLE ONLY "public"."venues"
    ADD CONSTRAINT "venues_business_id_foreign" FOREIGN KEY ("business_id") REFERENCES "public"."businesses"("id") ON DELETE CASCADE;


-- Completed on 2026-11-04 21:24:57

--
-- PostgreSQL database dump complete
--

\unrestrict KUIya2yQ14AVY982dPJvwevgqj9L7PVUrA4779547scGJK7fM9X0K0IvkozDvGa

