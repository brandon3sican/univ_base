# Database Structure Documentation

## Overview
This document outlines the complete database structure for the DENR CAR University Base system, including all tables, relationships, and data flow.

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

## STO (Strategic Targets and Outcomes) System Tables

### 8. `sto_universe`
**Purpose:** Stores universe values for STO calculations
**Created:** 2026_03_11_054512_create_sto_universe_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| office_ids | JSON | NULLABLE | Array of office IDs |
| values | JSON | NULLABLE | Array of corresponding universe values |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**JSON Structure:**
```json
{
  "office_ids": [1, 3, 7],
  "values": [100, 150, 200]
}
```

---

### 9. `sto_accomplishments`
**Purpose:** Stores accomplishment data by year and office
**Created:** 2026_03_11_054411_create_sto_accomplishments_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| office_ids | JSON | NULLABLE | Array of office IDs |
| values | JSON | NULLABLE | Array of accomplishment values |
| remarks | JSON | NULLABLE | Array of remarks for each office |
| years | JSON | NULLABLE | Array of years (2022-2026) |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**JSON Structure:**
```json
{
  "office_ids": [1, 3, 7],
  "values": [50, 75, 100],
  "remarks": ["Good", "Excellent", "Needs Improvement"],
  "years": [2022, 2023, 2024]
}
```

---

### 10. `sto_targets`
**Purpose:** Stores future target values by year
**Created:** 2026_03_11_054214_create_sto_targets_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| values | JSON | NULLABLE | Array of target values |
| years | JSON | NULLABLE | Array of target years (2027+) |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**JSON Structure:**
```json
{
  "values": [120, 180, 250],
  "years": [2027, 2028, 2029]
}
```

---

### 11. `sto` (Main STO Records)
**Purpose:** Main table linking PPAs with STO data
**Created:** 2026_03_11_054513_create_sto_table.php

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT (Primary) | Auto-increment | Unique identifier |
| ppa_id | BIGINT (Foreign) | `ppa(id)` ON DELETE SET NULL NULLABLE | Reference to PPA |
| indicator_id | BIGINT (Foreign) | `indicators(id)` ON DELETE SET NULL NULLABLE | Reference to indicator |
| universe_id | JSON | NULLABLE | Array of universe record IDs |
| accomplishment_id | JSON | NULLABLE | Array of accomplishment record IDs |
| targets_id | JSON | NULLABLE | Array of target record IDs |
| created_at | TIMESTAMP | | Creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `ppa`, `indicators`
- Has many (via JSON): `sto_universe`, `sto_accomplishments`, `sto_targets`

**JSON Fields:**
- `universe_id`: Array of references to `sto_universe` records
- `accomplishment_id`: Array of references to `sto_accomplishments` records  
- `targets_id`: Array of references to `sto_targets` records

---

## Relationships Diagram

```
office_types
    ↓ (1:N)
offices
    ↓ (via JSON)
ppa ← sto_universe
    ↓ (1:N)      ↓ (via JSON)
sto ← sto_accomplishments ← ppa_details
    ↓ (via JSON) ↓ (via JSON)
sto_targets ← indicators
    ↓ (via JSON)
record_types
    ↓ (1:N)
ppa ← types
```

## Data Flow

1. **Office Setup:**
   - `office_types` defines office categories
   - `offices` stores individual offices with type references

2. **PPA Hierarchy:**
   - `record_types` defines hierarchy levels (1-5)
   - `ppa_details` provides nested structure
   - `ppa` links everything together with office assignments

3. **STO System:**
   - `sto_universe` stores baseline values per office
   - `sto_accomplishments` tracks yearly achievements
   - `sto_targets` defines future goals
   - `sto` connects PPAs with all STO data

## Key Features

### JSON Arrays for Multi-Office Support
- `ppa.office_id`: [1, 3, 7] - Single PPA can span multiple offices
- `sto_universe.office_ids` & `values`: Parallel arrays for office-specific data
- `sto_accomplishments`: Similar structure with years and remarks

### Hierarchical Numbering System
- Record types 1-5 generate automatic numbering:
  - 1: PROGRAM → I., II., III.
  - 2: PROJECT → A., B., C.
  - 3: MAIN ACTIVITY → 1., 2., 3.
  - 4: SUB-ACTIVITY → 1.1., 1.2., 1.3.
  - 5: SUB-SUB-ACTIVITY → 1.1.1., 1.1.2., 1.1.3.

### STO Calculations
- **Baseline Formula:** `universe - sum(accomplishments 2022-2026)`
- **CAR Totals:** Summed across all offices for each metric
- **Year Separation:** Past years (2022-2025) vs Current year (2026) vs Future years (2027+)

## Color Coding System
- **PROGRAM:** #14423f (Dark Green)
- **PROJECT:** #306b40 (Medium Green)
- **MAIN ACTIVITY:** #66a558 (Light Green)
- **SUB-ACTIVITY:** #5c463e (Brown)
- **SUB-SUB-ACTIVITY:** #3a272b (Dark Brown)
- **Indicators & CAR:** #2563eb (Blue)

---

## Migration Order
1. `office_types` → `offices`
2. `record_types` → `ppa_details`
3. `indicators` → `types`
4. `ppa`
5. `sto_universe` → `sto_accomplishments` → `sto_targets` → `sto`

---

**Last Updated:** March 30, 2026
**Version:** 2.0
