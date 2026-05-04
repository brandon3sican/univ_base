# Database Schema and Diagrams

## Overview
Visual representation of the DENR CAR University Base database structure with relationships and data flow.

---

## Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    %% Laravel System Tables
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string remember_token
        timestamp created_at
        timestamp updated_at
    }
    
    cache {
        string key PK
        text value
        timestamp expiration
    }
    
    jobs {
        bigint id PK
        string queue
        text payload
        tinyint attempts
        tinyint reserved_at
        tinyint available_at
        timestamp created_at
    }
    
    %% Office Management
    office_types {
        bigint id PK
        varchar name UK
        text desc
        timestamp created_at
        timestamp updated_at
    }
    
    offices {
        bigint id PK
        varchar name
        bigint office_types_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    %% PPA Structure
    record_types {
        bigint id PK
        varchar name UK
        text desc
        timestamp created_at
        timestamp updated_at
    }
    
    ppa_details {
        bigint id PK
        bigint parent_id FK
        int column_order
        timestamp created_at
        timestamp updated_at
    }
    
    types {
        bigint id PK
        varchar code
        text desc
        timestamp created_at
        timestamp updated_at
    }
    
    indicators {
        bigint id PK
        varchar name
        timestamp created_at
        timestamp updated_at
    }
    
    ppa {
        bigint id PK
        varchar name
        bigint types_id FK
        bigint record_type_id FK
        bigint ppa_details_id FK
        bigint indicator_id FK
        json office_id
        timestamp created_at
        timestamp updated_at
    }
    
    %% Module Tables (all have identical structure)
    gass {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }
    
    sto {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }
    
    enf {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }
    
    biodiversity {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }
    
    lands {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }
    
    nra {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }
    
    soilcon {
        bigint id PK
        bigint ppa_id FK
        bigint indicator_id FK
        json office_id
        json universe
        json accomplishment
        json targets
        text remarks
        json years
        timestamp created_at
        timestamp updated_at
    }

    %% Relationships
    office_types ||--o{ offices : "has many"
    record_types ||--o{ ppa : "classifies"
    ppa_details ||--o{ ppa : "organizes"
    ppa_details ||--o{ ppa_details : "self-reference"
    types ||--o{ ppa : "categorizes"
    indicators ||--o{ ppa : "measures"
    
    %% Module Relationships
    ppa ||--o{ gass : "implements"
    ppa ||--o{ sto : "implements"
    ppa ||--o{ enf : "implements"
    ppa ||--o{ biodiversity : "implements"
    ppa ||--o{ lands : "implements"
    ppa ||--o{ nra : "implements"
    ppa ||--o{ soilcon : "implements"
    
    indicators ||--o{ gass : "tracks"
    indicators ||--o{ sto : "tracks"
    indicators ||--o{ enf : "tracks"
    indicators ||--o{ biodiversity : "tracks"
    indicators ||--o{ lands : "tracks"
    indicators ||--o{ nra : "tracks"
    indicators ||--o{ soilcon : "tracks"
```

---

## Data Flow Diagram

```mermaid
flowchart TD
    %% Office Setup
    OT[office_types] --> O[offices]
    
    %% PPA Structure
    RT[record_types] --> P[ppa]
    PD[ppa_details] --> P
    T[types] --> P
    I[indicators] --> P
    
    %% Module Tables
    P --> GASS[gass]
    P --> STO[sto]
    P --> ENF[enf]
    P --> BIO[biodiversity]
    P --> LAND[lands]
    P --> NRA[nra]
    P --> SOIL[soilcon]
    
    I --> GASS
    I --> STO
    I --> ENF
    I --> BIO
    I --> LAND
    I --> NRA
    I --> SOIL
    
    %% JSON Connections
    O -.->|office_id| P
    O -.->|office_id| GASS
    O -.->|office_id| STO
    O -.->|office_id| ENF
    O -.->|office_id| BIO
    O -.->|office_id| LAND
    O -.->|office_id| NRA
    O -.->|office_id| SOIL
    
    %% Self-reference
    PD --> PD
    
    classDef primary fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef secondary fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef module fill:#e8f5e8,stroke:#2e7d32,stroke-width:2px
    
    class OT,O,RT,PD,T,I primary
    class P secondary
    class GASS,STO,ENF,BIO,LAND,NRA,SOIL module
```

---

## Table Structure Overview

### 1. Office Management Layer
```mermaid
graph LR
    subgraph Office Management
        OT[office_types<br/>RO/PENRO/CENRO]
        O[offices<br/>Individual Offices]
    end
    
    OT -->|1:N| O
```

### 2. PPA Hierarchy Layer
```mermaid
graph TB
    subgraph PPA Structure
        RT[record_types<br/>1-5 Levels]
        PD[ppa_details<br/>Hierarchy]
        T[types<br/>Categories]
        I[indicators<br/>Metrics]
        P[ppa<br/>Main Records]
    end
    
    RT -->|classifies| P
    PD -->|organizes| P
    T -->|categorizes| P
    I -->|measures| P
    PD -->|self-ref| PD
```

### 3. Module System Layer
```mermaid
graph LR
    subgraph Module Tables
        GASS[gass<br/>GASS Module<br/>JSON Data]
        STO[sto<br/>STO Module<br/>JSON Data]
        ENF[enf<br/>ENF Module<br/>JSON Data]
        BIO[biodiversity<br/>Biodiversity Module<br/>JSON Data]
        LAND[lands<br/>Lands Module<br/>JSON Data]
        NRA[nra<br/>NRA Module<br/>JSON Data]
        SOIL[soilcon<br/>Soil Conservation<br/>JSON Data]
    end
    
    %% Note about JSON data structure
    note[Note: All module tables store<br/>universe, accomplishment,<br/>targets, remarks, years<br/>as JSON fields]
    note --> GASS
```

---

## JSON Field Structures

### PPA Office Assignment
```json
{
  "office_id": [1, 3, 7, 12],
  "description": "Array of office IDs for multi-office PPAs"
}
```

### Module Office Assignment
```json
{
  "office_id": [1, 3, 7],
  "description": "Array of office IDs for multi-office module records"
}
```

### Module Universe Data
```json
{
  "universe": [100, 150, 200],
  "office_id": [1, 3, 7],
  "mapping": "universe[i] ↔ office_id[i]"
}
```

### Module Accomplishment Data
```json
{
  "accomplishment": [50, 75, 100],
  "years": [2022, 2023, 2024],
  "remarks": ["Good", "Excellent", "Needs Improvement"],
  "office_id": [1, 3, 7]
}
```

### Module Target Data
```json
{
  "targets": [120, 180, 250],
  "years": [2027, 2028, 2029]
}
```

---

## Hierarchical PPA Structure

```mermaid
graph TD
    %% Program Level
    P1[I. PROGRAM NAME]
    
    %% Project Level
    P1 --> P2[A. PROJECT 1]
    P1 --> P3[B. PROJECT 2]
    
    %% Main Activity Level
    P2 --> P4[1. MAIN ACTIVITY 1.1]
    P2 --> P5[2. MAIN ACTIVITY 1.2]
    P3 --> P6[1. MAIN ACTIVITY 2.1]
    
    %% Sub Activity Level
    P4 --> P7[1.1. SUB-ACTIVITY 1.1.1]
    P4 --> P8[1.2. SUB-ACTIVITY 1.1.2]
    P5 --> P9[1.1. SUB-ACTIVITY 1.2.1]
    
    %% Sub-Sub Activity Level
    P7 --> P10[1.1.1. SUB-SUB-ACTIVITY 1.1.1.1]
    P7 --> P11[1.1.2. SUB-SUB-ACTIVITY 1.1.1.2]
    
    classDef program fill:#14423f,color:white
    classDef project fill:#306b40,color:white
    classDef main fill:#66a558,color:white
    classDef sub fill:#5c463e,color:white
    classDef subsub fill:#3a272b,color:white
    
    class P1 program
    class P2,P3 project
    class P4,P5,P6 main
    class P7,P8,P9 sub
    class P10,P11 subsub
```

---

## Module Calculation Flow

```mermaid
flowchart LR
    U[Universe Values] -->|per office| CALC[Baseline Calculation]
    ACC[Accomplishments<br/>2022-2026] -->|sum per office| CALC
    CALC --> BASELINE[Baseline<br/>Universe - Accomplishments]
    
    BASELINE -->|display| UI[Module Table<br/>(GASS/STO/ENF/etc.)]
    U -->|CAR total| UI
    ACC -->|CAR total| UI
    TARGETS[Target Values<br/>2027+] -->|CAR total| UI
    
    subgraph CAR Row
        CAR_TOT[CAR Totals<br/>Sum of all offices]
    end
    
    UI --> CAR_TOT
```

---

## Database Connection Points

### Primary Key Relationships
```mermaid
graph LR
    subgraph Foreign Keys
        office_types.id --> offices.office_types_id
        record_types.id --> ppa.record_type_id
        ppa_details.id --> ppa.ppa_details_id
        types.id --> ppa.types_id
        indicators.id --> ppa.indicator_id
        
        %% Module Foreign Keys
        ppa.id --> gass.ppa_id
        ppa.id --> sto.ppa_id
        ppa.id --> enf.ppa_id
        ppa.id --> biodiversity.ppa_id
        ppa.id --> lands.ppa_id
        ppa.id --> nra.ppa_id
        ppa.id --> soilcon.ppa_id
        
        indicators.id --> gass.indicator_id
        indicators.id --> sto.indicator_id
        indicators.id --> enf.indicator_id
        indicators.id --> biodiversity.indicator_id
        indicators.id --> lands.indicator_id
        indicators.id --> nra.indicator_id
        indicators.id --> soilcon.indicator_id
    end
```

### JSON Array Relationships
```mermaid
graph LR
    subgraph JSON Connections
        offices.id --> ppa.office_id
        offices.id --> gass.office_id
        offices.id --> sto.office_id
        offices.id --> enf.office_id
        offices.id --> biodiversity.office_id
        offices.id --> lands.office_id
        offices.id --> nra.office_id
        offices.id --> soilcon.office_id
    end
    
    %% Note about JSON array storage
    note[Note: Module tables store<br/>JSON data directly<br/>not foreign keys]
    note --> gass
```

---

## Data Volume Estimation

### Expected Records per Table
| Table | Estimated Records | Growth Rate |
|-------|------------------|-------------|
| users | 10-50 | Low |
| cache | Variable | High (auto-cleanup) |
| jobs | Variable | Medium |
| office_types | 3-5 | Static |
| offices | 15-20 | Low |
| record_types | 5 | Static |
| ppa_details | 100-500 | Medium |
| types | 10-20 | Low |
| indicators | 200-1000 | High |
| ppa | 500-2000 | High |
| gass | 200-800 | High |
| sto | 200-800 | High |
| enf | 200-800 | High |
| biodiversity | 200-800 | High |
| lands | 200-800 | High |
| nra | 200-800 | High |
| soilcon | 200-800 | High |

---

## Performance Considerations

### Index Strategy
```sql
-- Primary indexes (automatic)
PRIMARY KEY (id)

-- Foreign key indexes
INDEX (office_types_id)
INDEX (record_type_id)
INDEX (ppa_details_id)
INDEX (types_id)
INDEX (indicator_id)
INDEX (ppa_id)

-- JSON field indexes (MySQL 5.7+)
INDEX ((CAST(office_id AS CHAR(255) ARRAY)))
INDEX ((CAST(years AS CHAR(255) ARRAY)))

-- Self-reference index
INDEX (parent_id)
```

### Query Patterns
1. **PPA Hierarchy:** Recursive queries on `ppa_details.parent_id`
2. **Office Filtering:** JSON contains queries on `office_id` arrays
3. **STO Calculations:** JSON array aggregation across multiple tables
4. **Time-based Filtering:** Range queries on `years` JSON fields

---

## Migration Dependencies

```mermaid
graph TD
    %% Laravel System Tables
    U[users] --> C[cache]
    U --> J[jobs]
    
    %% Foundation Tables
    OT[office_types] --> O[offices]
    RT[record_types] --> PD[ppa_details]
    I[indicators] --> P[ppa]
    T[types] --> P
    PD --> P
    O --> P
    
    %% Module Tables (independent but depend on PPA and indicators)
    P --> GASS[gass]
    P --> STO[sto]
    P --> ENF[enf]
    P --> BIO[biodiversity]
    P --> LAND[lands]
    P --> NRA[nra]
    P --> SOIL[soilcon]
    
    I --> GASS
    I --> STO
    I --> ENF
    I --> BIO
    I --> LAND
    I --> NRA
    I --> SOIL
    
    classDef laravel fill:#ff5722
    classDef base fill:#ffeb3b
    classDef structure fill:#4caf50
    classDef module fill:#2196f3
    
    class U,C,J laravel
    class OT,O,RT,PD,I,T base
    class P structure
    class GASS,STO,ENF,BIO,LAND,NRA,SOIL module
```

---

## Summary Diagram

```mermaid
graph TB
    subgraph Laravel System Layer
        U[Users]
        C[Cache]
        J[Jobs]
    end
    
    subgraph Foundation Layer
        OT[Office Types]
        O[Offices]
        RT[Record Types]
        PD[PPA Details]
        T[Types]
        I[Indicators]
    end
    
    subgraph Business Layer
        P[PPA Records]
    end
    
    subgraph Module Layer
        GASS[GASS Module]
        STO[STO Module]
        ENF[ENF Module]
        BIO[Biodiversity Module]
        LAND[Lands Module]
        NRA[NRA Module]
        SOIL[Soil Conservation Module]
    end
    
    U --> C
    U --> J
    OT --> O
    RT --> P
    PD --> P
    T --> P
    I --> P
    O --> P
    P --> GASS
    P --> STO
    P --> ENF
    P --> BIO
    P --> LAND
    P --> NRA
    P --> SOIL
    I --> GASS
    I --> STO
    I --> ENF
    I --> BIO
    I --> LAND
    I --> NRA
    I --> SOIL
    
    classDef laravel fill:#ff5722,stroke:#d84315
    classDef foundation fill:#e3f2fd,stroke:#1976d2
    classDef business fill:#f3e5f5,stroke:#7b1fa2
    classDef module fill:#e8f5e8,stroke:#388e3c
    
    class U,C,J laravel
    class OT,O,RT,PD,T,I foundation
    class P business
    class GASS,STO,ENF,BIO,LAND,NRA,SOIL module
```

---

**Created:** May 4, 2026  
**Database Version:** 3.0  
**Diagram Tool:** Mermaid.js  
**Total Tables:** 17  
**Relationships:** 19 Foreign Keys + 8 JSON Arrays
