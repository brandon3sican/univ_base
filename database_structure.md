# Database Structure Documentation

## Overview
This document outlines the complete database structure for the DENR CAR University Base system, including all tables, relationships, and data flow.

---

## Laravel System Tables

### 1. `users`
**Purpose:** Laravel authentication system users
**Created:** 0001_01_01_000000_create_users_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| name | VARCHAR | | User name |
| email | VARCHAR | UNIQUE | User email |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| password | VARCHAR | | Hashed password |
| remember_token | VARCHAR | NULLABLE | "Remember me" token |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 2. `cache`
**Purpose:** Laravel cache system
**Created:** 0001_01_01_000001_create_cache_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| key | VARCHAR (Primary) | | Cache key |
| value | TEXT | | Cached data |
| expiration | INT | | Expiration timestamp |

---

### 3. `jobs`
**Purpose:** Laravel queue system jobs
**Created:** 0001_01_01_000002_create_jobs_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| queue | VARCHAR | | Queue name |
| payload | LONGTEXT | | Job data |
| attempts | TINYINT | | Number of attempts |
| reserved_at | INT | NULLABLE | Reservation timestamp |
| available_at | INT | | Available timestamp |
| created_at | TIMESTAMP | | Creation timestamp |

---

## Core Tables

### 1. `office_types`
**Purpose:** Defines different types of offices in the organization
**Created:** 2026_03_10_020703_create_office_types_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| name | VARCHAR | UNIQUE | Office type name (RO, PENRO, CENRO) |
| desc | TEXT | NULLABLE | Description of office type |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Sample Data:**
- RO (Regional Office)
- PENRO (Provincial Environment and Natural Resources Office)
- CENRO (Community Environment and Natural Resources Office)

---

### 2. `offices`
**Purpose:** Stores individual office records
**Created:** 2026_03_10_020704_create_offices_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| name | VARCHAR | | Office name |
| office_types_id | BIGINT (Foreign) | `office_types(id)` ON DELETE RESTRICT | Reference to office type |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `office_types`

---

### 3. `record_types`
**Purpose:** Defines hierarchical record types for PPAs
**Created:** 2026_03_10_020705_create_record_types_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| name | VARCHAR | UNIQUE | Record type name (PROGRAM, PROJECT, MAIN ACTIVITY, etc.) |
| desc | TEXT | NULLABLE | Description of record type |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Sample Data:**
- 1: PROGRAM
- 2: PROJECT
- 3: MAIN ACTIVITY
- 4: SUB-ACTIVITY
- 5: SUB-SUB-ACTIVITY

---

### 4. `ppa_details`
**Purpose:** Hierarchical structure for PPA organization
**Created:** 2026_03_10_020706_create_ppa_details_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| parent_id | BIGINT (Foreign) | `ppa_details(id)` ON DELETE CASCADE NULLABLE | Self-referencing parent |
| column_order | INTEGER | DEFAULT 0 | Order in hierarchy |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Self-referencing: `parent_id` → `ppa_details(id)`
- Supports unlimited nesting levels

---

### 5. `indicators`
**Purpose:** Stores output indicators for PPAs
**Created:** 2026_03_10_020826_create_indicators_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| name | VARCHAR | | Indicator name |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 6. `types`
**Purpose:** General type classifications
**Created:** 2026_03_10_020827_create_types_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| code | VARCHAR | | Type code |
| desc | TEXT | | Type description |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 7. `ppa` (Program, Project, Activity)
**Purpose:** Main table for storing PPAs with hierarchical structure
**Created:** 2026_03_10_030000_create_ppa_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| name | VARCHAR | | PPA name |
| types_id | BIGINT (Foreign) | `types(id)` ON DELETE RESTRICT | Reference to type |
| record_type_id | BIGINT (Foreign) | `record_types(id)` ON DELETE RESTRICT | Record type (1-5) |
| ppa_details_id | BIGINT (Foreign) | `ppa_details(id)` ON DELETE SET NULL NULLABLE | Hierarchical reference |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Associated indicator |
| office_id | JSON | NULLABLE | Array of office IDs [1, 3, 7] |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `types`, `record_types`, `ppa_details`, `indicators`
- Has many: `sto`

**JSON Fields:**
- `office_id`: Stores array of office IDs for multi-office assignments

---

## Module Tables (All Have Identical Structure)

### 8. `gass` (GASS Module)
**Purpose:** GASS (General Administrative Support Services) module data
**Created:** 2026_03_11_054513_create_gass.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 9. `sto` (STO Module)
**Purpose:** STO (Strategic Targets and Outcomes) module data
**Created:** 2026_03_11_054513_create_sto_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 10. `enf` (ENF Module)
**Purpose:** ENF (Environmental and Natural Resources) module data
**Created:** 2026_04_28_073958_enf.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 11. `biodiversity` (Biodiversity Module)
**Purpose:** Biodiversity conservation module data
**Created:** 2026_04_29_010257_biodiversity.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 12. `lands` (Lands Module)
**Purpose:** Lands management module data
**Created:** 2026_04_29_010257_lands.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 13. `nra` (NRA Module)
**Purpose:** NRA (Natural Resources Assessment) module data
**Created:** 2026_04_29_053143_create_nra_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

---

### 14. `soilcon` (Soil Conservation Module)
**Purpose:** Soil conservation module data
**Created:** 2026_04_29_053642_soilcon.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| office_id | JSON | NULLABLE | Array of office IDs |
| universe | JSON | NULLABLE | Array of universe values |
| accomplishment | JSON | NULLABLE | Array of accomplishment values |
| targets | JSON | NULLABLE | Array of target values |
| remarks | TEXT | NULLABLE | Remarks text |
| years | JSON | NULLABLE | Array of years |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Module JSON Structure:**
```json
{
  "office_id": [1, 3, 7],
  "universe": [100, 150, 200],
  "accomplishment": [50, 75, 100],
  "targets": [120, 180, 250],
  "years": [2022, 2023, 2024, 2027, 2028],
  "remarks": "General remarks for the record"
}
```

---

## Relationships Diagram

```
Laravel System:
users → cache
users → jobs

Office Management:
office_types
    ↓ (1:N)
offices
    ↓ (via JSON)
ppa ← gass
    ↓ (1:N)      ↓ (via JSON)
sto ← enf ← ppa_details
    ↓ (via JSON) ↓ (via JSON)
biodiversity ← indicators
lands ← record_types
nra ← types
soilcon

PPA Structure:
ppa_details (self-reference)
record_types → ppa
types → ppa
indicators → ppa
offices → ppa (via JSON)

Module Structure:
ppa → gass, sto, enf, biodiversity, lands, nra, soilcon
indicators → gass, sto, enf, biodiversity, lands, nra, soilcon
offices → all modules (via JSON office_id)
```

## Data Flow

1. **Laravel System:**
   - `users` provides authentication
   - `cache` stores application cache
   - `jobs` handles background tasks

2. **Office Setup:**
   - `office_types` defines office categories
   - `offices` stores individual offices with type references

3. **PPA Hierarchy:**
   - `record_types` defines hierarchy levels (1-6)
   - `ppa_details` provides nested structure
   - `ppa` links everything together with office assignments

4. **Module System:**
   - All 7 modules (gass, sto, enf, biodiversity, lands, nra, soilcon) have identical structure
   - Each module stores universe, accomplishment, targets as JSON fields
   - Modules link PPAs with indicators and office assignments

## Key Features

### JSON Arrays for Multi-Office Support
- `ppa.office_id`: [1, 3, 7] - Single PPA can span multiple offices
- Module `office_id`: [1, 3, 7] - Single module record can span multiple offices
- Module JSON fields store parallel arrays for office-specific data

### Hierarchical Numbering System
- Record types 1-6 generate automatic numbering:
  - 1: PROGRAM → I., II., III.
  - 2: PROJECT → A., B., C.
  - 3: MAIN ACTIVITY → 1., 2., 3.
  - 4: SUB-ACTIVITY → 1.1., 1.2., 1.3.
  - 5: SUB-SUB-ACTIVITY → 1.1.1., 1.1.2., 1.1.3.
  - 6: ACTIONABLE TASK → 1.1.1.1., 1.1.1.2., 1.1.1.3.

### Module Calculations
- **Baseline Formula:** `universe - sum(accomplishments 2022-2026)`
- **CAR Totals:** Summed across all offices for each metric
- **Year Separation:** Past years (2022-2025) vs Current year (2026) vs Future years (2027+)
- **Parent Activity Validation:** Record types 4, 5, 6 require parent activity selection

## Color Coding System
- **PROGRAM:** #14423f (Dark Green)
- **PROJECT:** #306b40 (Medium Green)
- **MAIN ACTIVITY:** #66a558 (Light Green)
- **SUB-ACTIVITY:** #5c463e (Brown)
- **SUB-SUB-ACTIVITY:** #3a272b (Dark Brown)
- **Indicators & CAR:** #2563eb (Blue)

---

## Migration Order
1. **Laravel System Tables:**
   - `users` → `cache` → `jobs`

2. **Foundation Tables:**
   - `office_types` → `offices`
   - `record_types` → `ppa_details`
   - `indicators` → `types`

3. **Business Logic:**
   - `ppa`

4. **Module Tables (independent, can run in parallel):**
   - `gass`
   - `sto`
   - `enf`
   - `biodiversity`
   - `lands`
   - `nra`
   - `soilcon`

---

**Last Updated:** May 4, 2026
**Version:** 3.0
**Total Tables:** 17
**Module Tables:** 7 (all identical structure)
