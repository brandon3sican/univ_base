# Database Schema Documentation

## Overview
This document outlines the database schema for the DENR CAR application, a Laravel-based system for managing various environmental programs and activities.

## Core Tables

### Users & Authentication

#### `users`
Standard Laravel users table for authentication and user management.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (Primary) | Auto-incrementing ID |
| name | STRING | User's full name |
| email | STRING (Unique) | User's email address |
| email_verified_at | TIMESTAMP (Nullable) | Email verification timestamp |
| password | STRING | Hashed password |
| remember_token | STRING (Nullable) | "Remember me" token |
| created_at | TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP | Record update timestamp |

#### `password_reset_tokens`
Stores password reset tokens for user authentication.

| Column | Type | Description |
|--------|------|-------------|
| email | STRING (Primary) | User's email address |
| token | STRING | Password reset token |
| created_at | TIMESTAMP (Nullable) | Token creation timestamp |

#### `sessions`
Laravel session management table.

| Column | Type | Description |
|--------|------|-------------|
| id | STRING (Primary) | Session ID |
| user_id | BIGINT (Nullable, Indexed) | Associated user ID |
| ip_address | STRING (45, Nullable) | User's IP address |
| user_agent | TEXT (Nullable) | Browser user agent |
| payload | LONGTEXT | Session data |
| last_activity | INTEGER (Indexed) | Last activity timestamp |

### System Tables

#### `cache`
Laravel cache storage.

| Column | Type | Description |
|--------|------|-------------|
| key | STRING (Primary) | Cache key |
| value | MEDIUMTEXT | Cached data |
| expiration | INTEGER (Indexed) | Expiration timestamp |

#### `cache_locks`
Cache locking mechanism.

| Column | Type | Description |
|--------|------|-------------|
| key | STRING (Primary) | Lock key |
| owner | STRING | Lock owner |
| expiration | INTEGER (Indexed) | Lock expiration |

#### `jobs`
Queue job management.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (Primary) | Job ID |
| queue | STRING (Indexed) | Queue name |
| payload | LONGTEXT | Job data |
| attempts | UNSIGNED TINYINT | Number of attempts |
| reserved_at | UNSIGNED INTEGER (Nullable) | Reservation timestamp |
| available_at | UNSIGNED INTEGER | Available timestamp |
| created_at | UNSIGNED INTEGER | Creation timestamp |

#### `job_batches`
Batch job management.

| Column | Type | Description |
|--------|------|-------------|
| id | STRING (Primary) | Batch ID |
| name | STRING | Batch name |
| total_jobs | INTEGER | Total jobs in batch |
| pending_jobs | INTEGER | Pending jobs |
| failed_jobs | INTEGER | Failed jobs |
| failed_job_ids | LONGTEXT | Failed job IDs |
| options | MEDIUMTEXT (Nullable) | Batch options |
| cancelled_at | INTEGER (Nullable) | Cancellation timestamp |
| created_at | INTEGER | Creation timestamp |
| finished_at | INTEGER (Nullable) | Completion timestamp |

#### `failed_jobs`
Failed job storage.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (Primary) | Failed job ID |
| uuid | STRING (Unique) | Job UUID |
| connection | TEXT | Connection name |
| queue | TEXT | Queue name |
| payload | LONGTEXT | Job data |
| exception | LONGTEXT | Exception details |
| failed_at | TIMESTAMP | Failure timestamp |

## Business Logic Tables

### GASS (General Appropriation Act Support System)

#### `gass`
Manages programs, projects, and activities with hierarchical structure and multi-year targets.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (Primary) | Auto-incrementing ID |
| program_project_activity | STRING | Name of program/project/activity |
| output_indicators | TEXT (Nullable) | Performance indicators |
| office | STRING (Nullable) | Responsible office |
| universe | TEXT (Nullable) | Target universe (comma-separated) |
| accomplishment | TEXT (Nullable) | Current accomplishments (comma-separated) |
| remarks | TEXT (Nullable) | Additional notes |
| target_2024 | STRING (Nullable) | 2024 target |
| target_2025 | STRING (Nullable) | 2025 target |
| target_2026 | STRING (Nullable) | 2026 target |
| target_2027 | STRING (Nullable) | 2027 target |
| target_2028 | STRING (Nullable) | 2028 target |
| parent_id | BIGINT (Nullable, Foreign) | Parent record ID |
| order_column | INTEGER | Sort order |
| record_type | STRING (Nullable) | Type of record |
| created_at | TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP | Record update timestamp |

**Relationships:**
- Self-referencing hierarchy via `parent_id` → `gass.id`
- `onDelete('cascade')` for parent-child relationships

**Computed Attributes:**
- `baseline`: Calculated as universe - accomplishment (comma-separated values)
- `total_targets`: Concatenated string of all year targets

### NRA (Natural Resources Accounting)

#### `nra`
Manages natural resources accounting data with hierarchical structure.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (Primary) | Auto-incrementing ID |
| program_project_activity | STRING | Name of program/project/activity |
| output_indicators | TEXT (Nullable) | Performance indicators |
| office | STRING (Nullable) | Responsible office |
| universe | INTEGER (Nullable) | Target universe |
| accomplishment | INTEGER (Nullable) | Current accomplishments |
| order_column | INTEGER | Sort order |
| parent_id | BIGINT (Nullable, Foreign) | Parent record ID |
| record_type | STRING (Nullable) | Type of record |
| created_at | TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP | Record update timestamp |

**Relationships:**
- Self-referencing hierarchy via `parent_id` → `nra.id`
- `onDelete('cascade')` for parent-child relationships

**Computed Attributes:**
- `baseline`: Calculated as universe - accomplishment
- `hierarchical_number`: Auto-generated numbering (1, 1.1, 1.1.1, etc.)
- `indentation_level`: Depth in hierarchy

### STO (Strategic Targets and Objectives)

#### `sto`
Manages strategic targets and objectives with hierarchical structure and multi-year targets.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (Primary) | Auto-incrementing ID |
| program_project_activity | STRING | Name of program/project/activity |
| output_indicators | TEXT (Nullable) | Performance indicators |
| office | STRING (Nullable) | Responsible office |
| universe | TEXT (Nullable) | Target universe (comma-separated) |
| accomplishment | TEXT (Nullable) | Current accomplishments (comma-separated) |
| remarks | TEXT (Nullable) | Additional notes |
| target_2024 | STRING (Nullable) | 2024 target |
| target_2025 | STRING (Nullable) | 2025 target |
| target_2026 | STRING (Nullable) | 2026 target |
| target_2027 | STRING (Nullable) | 2027 target |
| target_2028 | STRING (Nullable) | 2028 target |
| parent_id | BIGINT (Nullable, Foreign) | Parent record ID |
| order_column | INTEGER | Sort order |
| record_type | STRING (Nullable) | Type of record |
| created_at | TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP | Record update timestamp |

**Relationships:**
- Self-referencing hierarchy via `parent_id` → `sto.id`
- `onDelete('cascade')` for parent-child relationships

**Computed Attributes:**
- `baseline`: Calculated as universe - accomplishment (comma-separated values)
- `total_targets`: Concatenated string of all year targets

## Common Patterns

### Hierarchical Structure
All three business tables (`gass`, `nra`, `sto`) share a common hierarchical pattern:
- `parent_id` references the same table's `id`
- `order_column` for sorting siblings
- `record_type` for categorizing different levels
- Cascade delete maintains referential integrity

### Multi-Year Planning
`gass` and `sto` tables support 5-year planning (2024-2028) with individual target columns for each year.

### Computed Metrics
All business tables calculate baseline metrics (universe - accomplishment) either as single values (`nra`) or comma-separated values (`gass`, `sto`).

## Indexes and Performance

### Primary Keys
All tables use auto-incrementing BIGINT primary keys for optimal performance.

### Foreign Keys
- Self-referencing foreign keys in business tables with cascade delete
- Indexed for efficient hierarchical queries

### Specialized Indexes
- `users.email` (unique)
- `sessions.user_id` (indexed)
- `cache.expiration` (indexed)
- `cache_locks.expiration` (indexed)
- `jobs.queue` (indexed)

## Data Types Notes

### Text vs String
- `STRING` used for shorter, indexed fields
- `TEXT` used for longer descriptive fields
- `LONGTEXT` used for large data (payloads, session data)

### Numeric Fields
- `INTEGER` used for counts and IDs
- `BIGINT` used for primary keys to support large datasets
- `UNSIGNED` variants used where negative values are not needed

### Timestamps
- Standard Laravel `created_at` and `updated_at` timestamps
- Specialized timestamps for business logic (email verification, failure tracking)
