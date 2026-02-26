# Database Schema and Diagram Documentation

## Overview
This document provides a comprehensive overview of the database schema for the DENR CAR application, including entity relationships and visual diagrams.

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│      users      │       │      cache      │       │      jobs       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)        │       │ key (PK)       │       │ id (PK)        │
│ name           │       │ value           │       │ queue          │
│ email (UNIQUE) │       │ expiration      │       │ payload        │
│ email_verified  │       │                │       │ attempts       │
│ _at            │       │                │       │ reserved_at    │
│ password       │       │                │       │ available_at   │
│ remember_token │       │                │       │ created_at     │
│ created_at     │       │                │       │                │
│ updated_at     │       │                │       │                │
└─────────────────┘       └─────────────────┘       └─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│password_reset_  │       │    sessions     │       │  cache_locks   │
│     tokens     │       │                │       │                │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ email (PK)     │       │ id (PK)        │       │ key (PK)       │
│ token          │       │ user_id (FK)    │       │ owner          │
│ created_at     │       │ ip_address      │       │ expiration     │
│                │       │ user_agent      │       │                │
│                │       │ payload         │       │                │
│                │       │ last_activity   │       │                │
└─────────────────┘       └─────────────────┘       └─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  job_batches   │       │  failed_jobs   │       │      gass       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)        │       │ id (PK)        │       │ id (PK)        │
│ name           │       │ uuid (UNIQUE)   │       │ program_project │
│ total_jobs     │       │ connection      │       │ _activity      │
│ pending_jobs   │       │ queue           │       │ output_indicat │
│ failed_jobs    │       │ payload         │       │ ors            │
│ failed_job_ids │       │ exception       │       │ office          │
│ options        │       │ failed_at       │       │ universe        │
│ cancelled_at   │       │                │       │ accomplishment │
│ created_at     │       │                │       │ remarks         │
│ finished_at    │       │                │       │ target_2024     │
│                │       │                │       │ target_2025     │
│                │       │                │       │ target_2026     │
│                │       │                │       │ target_2027     │
│                │       │                │       │ target_2028     │
│                │       │                │       │ parent_id (FK)  │
│                │       │                │       │ order_column    │
│                │       │                │       │ record_type     │
│                │       │                │       │ created_at     │
│                │       │                │       │ updated_at     │
└─────────────────┘       └─────────────────┘       └─────────────────┘
                                                        │
                                                        │ (self-reference)
                                                        ▼
                                               ┌─────────────────┐
                                               │      gass       │
                                               │ (parent)       │
                                               ├─────────────────┤
                                               │ id (PK)        │
                                               │ ...            │
                                               └─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│       nra      │       │       sto       │       │      enf       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)        │       │ id (PK)        │       │ id (PK)        │
│ program_project │       │ program_project │       │ program_project │
│ _activity      │       │ _activity      │       │ _activity      │
│ output_indicat │       │ output_indicat │       │ output_indicat │
│ ors            │       │ ors            │       │ ors            │
│ office          │       │ office          │       │ office          │
│ universe        │       │ universe        │       │ universe        │
│ accomplishment │       │ accomplishment │       │ accomplishment │
│ order_column    │       │ remarks         │       │ remarks         │
│ parent_id (FK)  │       │ target_2024     │       │ target_2024     │
│ record_type     │       │ target_2025     │       │ target_2025     │
│ created_at     │       │ target_2026     │       │ target_2026     │
│ updated_at     │       │ target_2027     │       │ target_2027     │
│                │       │ target_2028     │       │ target_2028     │
│                │       │ parent_id (FK)  │       │ parent_id (FK)  │
│                │       │ order_column    │       │ order_column    │
│                │       │ record_type     │       │ record_type     │
│                │       │ created_at     │       │ created_at     │
│                │       │ updated_at     │       │ updated_at     │
└─────────────────┘       └─────────────────┘       └─────────────────┘
        │                       │                       │
        │ (self-reference)       │ (self-reference)       │ (self-reference)
        ▼                       ▼                       ▼
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│       nra      │       │       sto       │       │      enf       │
│ (parent)       │       │ (parent)       │       │ (parent)       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)        │       │ id (PK)        │       │ id (PK)        │
│ ...            │       │ ...            │       │ ...            │
└─────────────────┘       └─────────────────┘       └─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  biodiversity  │       │     lands       │       │    soilcon     │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)        │       │ id (PK)        │       │ id (PK)        │
│ program_project │       │ program_project │       │ program_project │
│ _activity      │       │ _activity      │       │ _activity      │
│ output_indicat │       │ output_indicat │       │ output_indicat │
│ ors            │       │ ors            │       │ ors            │
│ office          │       │ office          │       │ office          │
│ universe        │       │ universe        │       │ universe        │
│ accomplishment │       │ accomplishment │       │ accomplishment │
│ remarks         │       │ remarks         │       │ remarks         │
│ target_2024     │       │ target_2024     │       │ target_2024     │
│ target_2025     │       │ target_2025     │       │ target_2025     │
│ target_2026     │       │ target_2026     │       │ target_2026     │
│ target_2027     │       │ target_2027     │       │ target_2027     │
│ target_2028     │       │ target_2028     │       │ target_2028     │
│ parent_id (FK)  │       │ parent_id (FK)  │       │ parent_id (FK)  │
│ order_column    │       │ order_column    │       │ order_column    │
│ record_type     │       │ record_type     │       │ record_type     │
│ created_at     │       │ created_at     │       │ created_at     │
│ updated_at     │       │ updated_at     │       │ updated_at     │
└─────────────────┘       └─────────────────┘       └─────────────────┘
        │                       │                       │
        │ (self-reference)       │ (self-reference)       │ (self-reference)
        ▼                       ▼                       ▼
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  biodiversity  │       │     lands       │       │    soilcon     │
│ (parent)       │       │ (parent)       │       │ (parent)       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)        │       │ id (PK)        │       │ id (PK)        │
│ ...            │       │ ...            │       │ ...            │
└─────────────────┘       └─────────────────┘       └─────────────────┘
```

## Detailed Schema

### 1. Authentication & User Management

#### `users`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | User identifier |
| name | STRING | NOT NULL | User's full name |
| email | STRING | NOT NULL, UNIQUE | User's email address |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| password | STRING | NOT NULL | Hashed password |
| remember_token | STRING | NULLABLE | "Remember me" token |
| created_at | TIMESTAMP | NOT NULL | Account creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Last update timestamp |

#### `password_reset_tokens`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| email | STRING | PRIMARY KEY | User's email address |
| token | STRING | NOT NULL | Password reset token |
| created_at | TIMESTAMP | NULLABLE | Token creation timestamp |

#### `sessions`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | STRING | PRIMARY KEY | Session identifier |
| user_id | BIGINT | NULLABLE, FOREIGN KEY → users.id | Associated user |
| ip_address | STRING(45) | NULLABLE | User's IP address |
| user_agent | TEXT | NULLABLE | Browser user agent |
| payload | LONGTEXT | NOT NULL | Session data |
| last_activity | INTEGER | INDEXED | Last activity timestamp |

### 2. System Infrastructure

#### `cache`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| key | STRING | PRIMARY KEY | Cache key |
| value | MEDIUMTEXT | NOT NULL | Cached data |
| expiration | INTEGER | INDEXED | Expiration timestamp |

#### `cache_locks`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| key | STRING | PRIMARY KEY | Lock key |
| owner | STRING | NOT NULL | Lock owner |
| expiration | INTEGER | INDEXED | Lock expiration |

#### `jobs`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Job identifier |
| queue | STRING | INDEXED | Queue name |
| payload | LONGTEXT | NOT NULL | Job data |
| attempts | UNSIGNED TINYINT | NOT NULL | Number of attempts |
| reserved_at | UNSIGNED INTEGER | NULLABLE | Reservation timestamp |
| available_at | UNSIGNED INTEGER | NOT NULL | Available timestamp |
| created_at | UNSIGNED INTEGER | NOT NULL | Creation timestamp |

#### `job_batches`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | STRING | PRIMARY KEY | Batch identifier |
| name | STRING | NOT NULL | Batch name |
| total_jobs | INTEGER | NOT NULL | Total jobs in batch |
| pending_jobs | INTEGER | NOT NULL | Pending jobs |
| failed_jobs | INTEGER | NOT NULL | Failed jobs |
| failed_job_ids | LONGTEXT | NOT NULL | Failed job IDs |
| options | MEDIUMTEXT | NULLABLE | Batch options |
| cancelled_at | INTEGER | NULLABLE | Cancellation timestamp |
| created_at | INTEGER | NOT NULL | Creation timestamp |
| finished_at | INTEGER | NULLABLE | Completion timestamp |

#### `failed_jobs`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Failed job ID |
| uuid | STRING | UNIQUE | Job UUID |
| connection | TEXT | NOT NULL | Connection name |
| queue | TEXT | NOT NULL | Queue name |
| payload | LONGTEXT | NOT NULL | Job data |
| exception | LONGTEXT | NOT NULL | Exception details |
| failed_at | TIMESTAMP | NOT NULL | Failure timestamp |

### 3. Business Logic Tables

#### `gass` (General Appropriation Act Support System)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | TEXT | NULLABLE | Target universe (comma-separated) |
| accomplishment | TEXT | NULLABLE | Current accomplishments (comma-separated) |
| remarks | TEXT | NULLABLE | Additional notes |
| target_2024 | STRING | NULLABLE | 2024 target |
| target_2025 | STRING | NULLABLE | 2025 target |
| target_2026 | STRING | NULLABLE | 2026 target |
| target_2027 | STRING | NULLABLE | 2027 target |
| target_2028 | STRING | NULLABLE | 2028 target |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → gass.id | Parent record |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

#### `nra` (Natural Resources Accounting)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | INTEGER | NULLABLE | Target universe |
| accomplishment | INTEGER | NULLABLE | Current accomplishments |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → nra.id | Parent record |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

#### `sto` (Strategic Targets and Objectives)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | TEXT | NULLABLE | Target universe (comma-separated) |
| accomplishment | TEXT | NULLABLE | Current accomplishments (comma-separated) |
| remarks | TEXT | NULLABLE | Additional notes |
| target_2024 | STRING | NULLABLE | 2024 target |
| target_2025 | STRING | NULLABLE | 2025 target |
| target_2026 | STRING | NULLABLE | 2026 target |
| target_2027 | STRING | NULLABLE | 2027 target |
| target_2028 | STRING | NULLABLE | 2028 target |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → sto.id | Parent record |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

#### `enf` (Environmental and Natural Resources)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | TEXT | NULLABLE | Target universe (comma-separated) |
| accomplishment | TEXT | NULLABLE | Current accomplishments (comma-separated) |
| remarks | TEXT | NULLABLE | Additional notes |
| target_2024 | STRING | NULLABLE | 2024 target |
| target_2025 | STRING | NULLABLE | 2025 target |
| target_2026 | STRING | NULLABLE | 2026 target |
| target_2027 | STRING | NULLABLE | 2027 target |
| target_2028 | STRING | NULLABLE | 2028 target |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → enf.id | Parent record |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

#### `biodiversity` (Biodiversity Conservation)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | TEXT | NULLABLE | Target universe (comma-separated) |
| accomplishment | TEXT | NULLABLE | Current accomplishments (comma-separated) |
| remarks | TEXT | NULLABLE | Additional notes |
| target_2024 | STRING | NULLABLE | 2024 target |
| target_2025 | STRING | NULLABLE | 2025 target |
| target_2026 | STRING | NULLABLE | 2026 target |
| target_2027 | STRING | NULLABLE | 2027 target |
| target_2028 | STRING | NULLABLE | 2028 target |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → biodiversity.id | Parent record |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

#### `lands` (Land Management)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | TEXT | NULLABLE | Target universe (comma-separated) |
| accomplishment | TEXT | NULLABLE | Current accomplishments (comma-separated) |
| remarks | TEXT | NULLABLE | Additional notes |
| target_2024 | STRING | NULLABLE | 2024 target |
| target_2025 | STRING | NULLABLE | 2025 target |
| target_2026 | STRING | NULLABLE | 2026 target |
| target_2027 | STRING | NULLABLE | 2027 target |
| target_2028 | STRING | NULLABLE | 2028 target |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → lands.id | Parent record |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

#### `soilcon` (Soil Conservation)
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Record identifier |
| program_project_activity | STRING | NOT NULL | Program/Project/Activity name |
| output_indicators | TEXT | NULLABLE | Performance indicators |
| office | STRING | NULLABLE | Responsible office |
| universe | TEXT | NULLABLE | Target universe (comma-separated) |
| accomplishment | TEXT | NULLABLE | Current accomplishments (comma-separated) |
| remarks | TEXT | NULLABLE | Additional notes |
| target_2024 | STRING | NULLABLE | 2024 target |
| target_2025 | STRING | NULLABLE | 2025 target |
| target_2026 | STRING | NULLABLE | 2026 target |
| target_2027 | STRING | NULLABLE | 2027 target |
| target_2028 | STRING | NULLABLE | 2028 target |
| parent_id | BIGINT | NULLABLE, FOREIGN KEY → soilcon.id | Parent record |
| order_column | INTEGER | DEFAULT 0 | Sort order |
| record_type | STRING | NULLABLE | Record type (program/project/activity) |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Update timestamp |

## Relationships

### Hierarchical Structure
All business tables (`gass`, `nra`, `sto`, `enf`, `biodiversity`, `lands`, `soilcon`) implement self-referencing hierarchies:

```
Program (parent_id = NULL)
├── Project (parent_id = program_id)
│   ├── Activity (parent_id = project_id)
│   └── Sub-activity (parent_id = activity_id)
└── Direct Activity (parent_id = program_id)
    └── Sub-activity (parent_id = activity_id)
```

### Foreign Key Constraints
- `sessions.user_id` → `users.id` (ON DELETE SET NULL)
- `gass.parent_id` → `gass.id` (ON DELETE CASCADE)
- `nra.parent_id` → `nra.id` (ON DELETE CASCADE)
- `sto.parent_id` → `sto.id` (ON DELETE CASCADE)
- `enf.parent_id` → `enf.id` (ON DELETE CASCADE)
- `biodiversity.parent_id` → `biodiversity.id` (ON DELETE CASCADE)
- `lands.parent_id` → `lands.id` (ON DELETE CASCADE)
- `soilcon.parent_id` → `soilcon.id` (ON DELETE CASCADE)

## Indexes

### Primary Keys
All tables use auto-incrementing BIGINT primary keys for optimal performance.

### Unique Constraints
- `users.email` - Ensures unique email addresses
- `failed_jobs.uuid` - Ensures unique job UUIDs

### Indexes for Performance
- `sessions.user_id` - Fast user session lookups
- `sessions.last_activity` - Session cleanup queries
- `cache.expiration` - Cache expiration queries
- `cache_locks.expiration` - Lock expiration queries
- `jobs.queue` - Queue processing

## Data Flow Patterns

### 1. User Authentication Flow
```
users ←→ sessions ←→ password_reset_tokens
```

### 2. Job Processing Flow
```
jobs → job_batches → failed_jobs
```

### 3. Business Data Flow
```
gass/nra/sto/enf/biodiversity/lands/soilcon (hierarchical)
├── Programs (root level)
├── Projects (child of programs)
├── Activities (child of projects or programs)
└── Sub-activities (child of activities)
```

## Computed Attributes

### GASS, STO, ENF, Biodiversity, Lands, & Soilcon Tables
- **baseline**: `universe - accomplishment` (comma-separated calculation)
- **total_targets**: Concatenated string of all year targets

### NRA Table
- **baseline**: `universe - accomplishment` (single value calculation)
- **hierarchical_number**: Auto-generated numbering (1, 1.1, 1.1.1, etc.)
- **indentation_level**: Depth in hierarchy

## Multi-Year Planning Support

### Target Years (2024-2028)
All business tables (`gass`, `sto`, `enf`, `biodiversity`, `lands`, `soilcon`) support 5-year planning with individual target columns:
- `target_2024` through `target_2028`
- Allows for strategic planning and progress tracking

### Universe/Accomplishment Tracking
- **GASS, STO, ENF, Biodiversity, Lands, & Soilcon**: TEXT fields supporting comma-separated values for multiple offices
- **NRA**: INTEGER fields for single-value tracking

## Migration History

### Initial Migration (0001_01_01_000000)
- Created `users`, `password_reset_tokens`, `sessions`

### System Migrations (0001_01_01_000001-000002)
- Created cache and job management tables

### Business Logic Migrations
- `2024_02_25_000001_create_gass_table.php`
- `2026_02_11_000000_create_nra_table_combined.php`
- `2026_02_11_000001_create_sto_table.php`
- `2026_02_11_000002_create_enf_table.php` (planned)
- `2026_02_11_000003_create_biodiversity_table.php` (planned)
- `2026_02_11_000004_create_lands_table.php` (planned)
- `2026_02_11_000005_create_soilcon_table.php` (planned)

## Performance Considerations

### Optimizations
1. **Hierarchical Queries**: Use `order_column` for efficient sorting
2. **Index Strategy**: Strategic indexes on frequently queried columns
3. **Data Types**: Appropriate data types to minimize storage
4. **Cascade Deletes**: Maintain referential integrity automatically

### Scaling Considerations
1. **Partitioning**: Consider partitioning large tables by date
2. **Archive Strategy**: Implement archiving for old job records
3. **Connection Pooling**: Optimize database connections
4. **Query Optimization**: Use appropriate indexes for complex queries

## Security Considerations

### Data Protection
1. **Password Hashing**: All passwords are hashed
2. **Session Security**: Secure session management
3. **Input Validation**: Server-side validation for all inputs
4. **CSRF Protection**: Built-in Laravel CSRF protection

### Access Control
1. **User Authentication**: Secure login system
2. **Authorization**: Role-based access control
3. **Audit Trail**: Timestamps for all record changes
