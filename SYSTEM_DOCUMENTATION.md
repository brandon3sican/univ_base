# DENR CAR University Base System Documentation

## Overview

The DENR CAR (Department of Environment and Natural Resources - Cordillera Administrative Region) University Base system is a comprehensive web-based application for managing Programs, Projects, and Activities (PPAs) across multiple environmental sectors. The system tracks universe values, baselines, accomplishments, and targets for various environmental initiatives across different office types (Regional Office, PENRO, CENRO).

**Technology Stack:**
- Backend: Laravel (PHP Framework)
- Frontend: Blade Templates with TailwindCSS
- Database: MySQL with JSON field support
- Authentication: Laravel's built-in authentication system with role-based access control

---

## System Architecture

### Core Modules

The system consists of 7 main sector modules, each with identical structure and functionality:

1. **GASS** - General Administrative Support Services
2. **STO** - Service to Operations
3. **ENF** - Environmental and Natural Resources
4. **BIODIVERSITY** - Biodiversity Conservation
5. **LANDS** - Lands Management
6. **SOILCON** - Soil Conservation
7. **NRA** - Natural Resources Assessment

### Database Structure

The system uses 17 database tables organized into four layers:

**Laravel System Tables:**
- `users` - User authentication and management
- `cache` - Application caching
- `jobs` - Background job processing

**Foundation Tables:**
- `office_types` - Office type definitions (RO, PENRO, CENRO)
- `offices` - Individual office records
- `record_types` - PPA hierarchy levels (1-6)
- `ppa_details` - Hierarchical PPA structure
- `types` - General type classifications
- `indicators` - Output indicators for PPAs

**Business Logic Table:**
- `ppa` - Main PPA records with hierarchical structure

**Module Tables:**
- `gass`, `sto`, `enf`, `biodiversity`, `lands`, `nra`, `soilcon` - Module-specific data

*For detailed database schema and relationships, refer to `database_structure.md` and `db-schema-and-diagram.md`*

---

## Features and Functionality

### 1. Authentication and Authorization

**User Roles:**
- **Admin** - Full system access, can manage users, approve/disapprove, assign divisions and PENROs
- **Chief PMD** - Can approve/disapprove, assign divisions and PENROs
- **PMD Division** - Can approve/disapprove, assign divisions
- **Other Division** - Can assign divisions and PENROs
- **PENRO** - Standard user access

**Authentication Features:**
- Login/logout functionality
- Session management
- Role-based access control
- Permission checks for user management, approvals, and assignments

### 2. Dashboard

**Main Dashboard Features:**
- **KPI Cards** - Display total universe, baseline, accomplishment, and overall progress across all sectors
- **Sector Summary** - Individual cards for each of the 7 modules showing:
  - Record count (UB Count)
  - Total Universe
  - Total Baseline
  - Total Accomplishment
- **Sector Drill-down** - Click on any sector to view detailed records
- **Search Functionality** - Search across all modules by:
  - Keyword (PPA name or indicator)
  - Location (specific module or all)
  - Office (specific office or all)

**Dashboard Calculations:**
- **Baseline Formula**: `universe - sum(accomplishments from 2022-2026)`
- **Overall Progress**: `((totalUniverse - totalBaseline) / totalUniverse) * 100`
- **Year Separation**: Past years (2022-2025), Current year (2026), Future years (2027+)

### 3. PPA Management

**PPA Hierarchy System:**
The system supports 6 levels of hierarchical structure:

1. **PROGRAM** (Level 1) - Numbered as I., II., III.
2. **PROJECT** (Level 2) - Numbered as A., B., C.
3. **MAIN ACTIVITY** (Level 3) - Numbered as 1., 2., 3.
4. **SUB-ACTIVITY** (Level 4) - Numbered as 1.1., 1.2., 1.3.
5. **SUB-SUB-ACTIVITY** (Level 5) - Numbered as 1.1.1., 1.1.2., 1.1.3.
6. **ACTIONABLE TASK** (Level 6) - Numbered as 1.1.1.1., 1.1.1.2., 1.1.1.3.

**PPA Features:**
- Hierarchical numbering system with automatic generation
- Multi-office assignment via JSON arrays
- Indicator association
- Type classification
- Parent-child relationships for nested structures
- Color-coded display by record type

**Color Coding:**
- PROGRAM: #14423f (Dark Green)
- PROJECT: #306b40 (Medium Green)
- MAIN ACTIVITY: #66a558 (Light Green)
- SUB-ACTIVITY: #5c463e (Brown)
- SUB-SUB-ACTIVITY: #3a272b (Dark Brown)
- Indicators & CAR: #2563eb (Blue)

### 4. Module Management

Each of the 7 modules (GASS, STO, ENF, BIODIVERSITY, LANDS, SOILCON, NRA) provides:

**CRUD Operations:**
- **Create** - Add new module records with PPA and indicator selection
- **Read** - View module records with filtering by PPA name
- **Update** - Edit existing records with change tracking
- **Delete** - Remove records with confirmation

**Data Fields:**
- **PPA Selection** - Link to hierarchical PPA structure
- **Indicator Selection** - Associate with output indicators
- **Office Assignment** - Multi-office support via JSON arrays
- **Universe Values** - Baseline universe data per office
- **Accomplishment Data** - Yearly accomplishments per office (2022-2028)
- **Target Values** - Future targets per office (2027+)
- **Remarks** - Text notes for each record
- **Years** - Array of years for data tracking

**JSON Data Structure:**
```json
{
  "office_id": [1, 3, 7],
  "universe": [100, 150, 200],
  "accomplishment": {
    "1": {"2022": 50, "2023": 75, "2024": 100},
    "3": {"2022": 60, "2023": 80, "2024": 110},
    "7": {"2022": 70, "2023": 90, "2024": 120}
  },
  "targets": {
    "1": {"2027": 120, "2028": 150},
    "3": {"2027": 130, "2028": 160},
    "7": {"2027": 140, "2028": 170}
  },
  "years": [2022, 2023, 2024, 2027, 2028],
  "remarks": "General remarks for the record"
}
```

### 5. Office Management

**Office Types:**
- **RO** - Regional Office
- **PENRO** - Provincial Environment and Natural Resources Office
- **CENRO** - Community Environment and Natural Resources Office

**Office Features:**
- Type-based classification
- Multi-office assignment to PPAs and module records
- Office-specific data tracking via JSON arrays
- CAR (Cordillera Administrative Region) totals aggregation

### 6. Edit History Tracking

**Edit History Features:**
- Automatic logging of all create, update, and delete operations
- Tracks user who made the change
- Records model type and ID
- Stores change details (old vs new values)
- Provides description of the action
- Viewable through dedicated edit history interface

**Edit History Data:**
- User ID
- Model type (e.g., "App\Models\Gass")
- Model ID
- Action (created, updated, deleted)
- Changes (JSON diff)
- Description (human-readable)
- Timestamp

### 7. User Management

**User Management Features:**
- Create new users
- Edit existing user information
- Delete users
- Role assignment
- Permission-based access control

**User Permissions:**
- `canManageUsers()` - Admin only
- `canApproveDisapprove()` - Admin, Chief PMD, PMD Division
- `canAssignDivision()` - Admin, Chief PMD, PMD Division, Other Division
- `canAssignPenro()` - Admin, Chief PMD, Other Division

---

## API Endpoints

### Public API Routes (No Authentication Required)

**PPA Retrieval by Record Type:**
- `GET /api/ppas` - Get GASS PPAs by record type
- `GET /api/lands/ppas` - Get LANDS PPAs by record type
- `GET /api/sto/ppas` - Get STO PPAs by record type
- `GET /api/enf/ppas` - Get ENF PPAs by record type
- `GET /api/biodiversity/ppas` - Get BIODIVERSITY PPAs by record type
- `GET /api/soilcon/ppas` - Get SOILCON PPAs by record type
- `GET /api/nra/ppas` - Get NRA PPAs by record type

### Authentication Routes

- `GET /` - Redirect to login
- `GET /login` - Show login form
- `POST /login` - Process login
- `POST /logout` - Process logout

### Protected Routes (Authentication Required)

**Dashboard:**
- `GET /dashboard` - Main dashboard
- `GET /dashboard/sector/{sector}` - Sector-specific dashboard
- `POST /dashboard/search` - Search across modules

**Module Routes (for each of the 7 modules):**

**GASS Module:**
- `GET /gass` - Index (list all records)
- `GET /gass/create` - Create form
- `POST /gass` - Store new record
- `GET /gass/{id}` - Show single record
- `GET /gass/{id}/edit` - Edit form
- `PUT /gass/{id}` - Update record
- `DELETE /gass/{id}` - Delete record

**STO Module:**
- `GET /sto` - Index
- `GET /sto/create` - Create form
- `POST /sto` - Store
- `GET /sto/{id}` - Show
- `GET /sto/{id}/edit` - Edit
- `PUT /sto/{id}` - Update
- `DELETE /sto/{id}` - Delete

**ENF Module:**
- `GET /enf` - Index
- `GET /enf/create` - Create form
- `POST /enf` - Store
- `GET /enf/{id}` - Show
- `GET /enf/{id}/edit` - Edit
- `PUT /enf/{id}` - Update
- `DELETE /enf/{id}` - Delete

**LANDS Module:**
- `GET /lands` - Index
- `GET /lands/create` - Create form
- `POST /lands` - Store
- `GET /lands/{id}` - Show
- `GET /lands/{id}/edit` - Edit
- `PUT /lands/{id}` - Update
- `DELETE /lands/{id}` - Delete

**BIODIVERSITY Module:**
- `GET /biodiversity` - Index
- `GET /biodiversity/create` - Create form
- `POST /biodiversity` - Store
- `GET /biodiversity/{id}` - Show
- `GET /biodiversity/{id}/edit` - Edit
- `PUT /biodiversity/{id}` - Update
- `DELETE /biodiversity/{id}` - Delete

**SOILCON Module:**
- `GET /soilcon` - Index
- `GET /soilcon/create` - Create form
- `POST /soilcon` - Store
- `GET /soilcon/{id}` - Show
- `GET /soilcon/{id}/edit` - Edit
- `PUT /soilcon/{id}` - Update
- `DELETE /soilcon/{id}` - Delete

**NRA Module:**
- `GET /nra` - Index
- `GET /nra/create` - Create form
- `POST /nra` - Store
- `GET /nra/{id}` - Show
- `GET /nra/{id}/edit` - Edit
- `PUT /nra/{id}` - Update
- `DELETE /nra/{id}` - Delete

**User Management:**
- `GET /user-management` - Index (list all users)
- `GET /user-management/create` - Create form
- `POST /user-management` - Store new user
- `GET /user-management/{id}/edit` - Edit form
- `PUT /user-management/{id}` - Update user
- `DELETE /user-management/{id}` - Delete user

**Edit History:**
- `GET /edit-history` - Index (list all edit history)
- `GET /edit-history/{id}` - Show specific edit history

---

## Business Processes

### 1. PPA Creation Process

1. **Define Record Types** - Set up hierarchy levels (PROGRAM, PROJECT, etc.)
2. **Create PPA Details** - Establish hierarchical structure with parent-child relationships
3. **Define Indicators** - Create output indicators for measurement
4. **Create PPA Records** - Link PPA to types, record types, indicators, and offices
5. **Assign Offices** - Use JSON arrays to assign multiple offices to a PPA

### 2. Module Data Entry Process

1. **Select Module** - Choose from GASS, STO, ENF, BIODIVERSITY, LANDS, SOILCON, or NRA
2. **Choose PPA** - Select from hierarchical PPA structure
3. **Select Indicator** - Associate with appropriate output indicator
4. **Assign Offices** - Select one or more offices for data tracking
5. **Enter Universe Values** - Input baseline universe data per office
6. **Enter Accomplishments** - Input yearly accomplishments (2022-2026) per office
7. **Set Targets** - Define future targets (2027+) per office
8. **Add Remarks** - Include optional notes
9. **Save Record** - System automatically logs edit history

### 3. Dashboard Reporting Process

1. **Aggregate Data** - System calculates totals across all modules
2. **Calculate Baselines** - Apply formula: `universe - accomplishments(2022-2026)`
3. **Compute Progress** - Calculate overall progress percentage
4. **Display KPIs** - Show universe, baseline, accomplishment, progress cards
5. **Sector Breakdown** - Display individual module statistics
6. **Enable Drill-down** - Allow users to click sectors for detailed views
7. **Support Search** - Enable filtering by keyword, location, and office

### 4. Edit History Process

1. **User Action** - User performs create, update, or delete operation
2. **Automatic Logging** - System captures:
   - User ID
   - Model type and ID
   - Action type
   - Change details (old vs new values)
   - Description
   - Timestamp
3. **Error Handling** - Logs errors if history tracking fails
4. **History Viewing** - Admins can view complete edit history
5. **Audit Trail** - Provides complete audit trail for compliance

### 5. User Management Process

1. **User Creation** - Admin creates new user account
2. **Role Assignment** - Assign appropriate role (admin, chief-pmd, etc.)
3. **Permission Application** - System applies role-based permissions
4. **User Modification** - Admin can edit user details and roles
5. **User Deletion** - Admin can remove users (with confirmation)
6. **Access Control** - System enforces permissions on all protected routes

---

## Data Flow

### 1. PPA Hierarchy Flow

```
Record Types → PPA Details → PPA Records → Module Records
     ↓              ↓              ↓              ↓
  Levels 1-6    Structure    Office Assign   Data Entry
```

### 2. Module Data Flow

```
PPA Selection → Indicator Selection → Office Assignment
     ↓                 ↓                    ↓
Universe Entry → Accomplishment Entry → Target Setting
     ↓                 ↓                    ↓
   JSON Storage → Baseline Calculation → Dashboard Display
```

### 3. Dashboard Calculation Flow

```
Module Records → JSON Parsing → Universe Sum
                                    ↓
Accomplishment Sum (2022-2026) → Baseline Calculation
                                    ↓
                            Progress Calculation
                                    ↓
                            KPI Card Display
```

### 4. Edit History Flow

```
User Action → Controller Method → Model Operation
                                    ↓
                            Edit History Trait
                                    ↓
                            History Record Creation
                                    ↓
                            Database Storage
```

---

## Security Features

### Authentication
- Laravel's built-in authentication system
- Session-based authentication
- Password hashing
- "Remember me" functionality

### Authorization
- Role-based access control (RBAC)
- Permission checks on sensitive operations
- Middleware protection on routes
- User role validation

### Data Protection
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- CSRF protection on all forms
- Input validation
- JSON field sanitization

### Audit Trail
- Complete edit history tracking
- User attribution for all changes
- Timestamp recording
- Change detail logging

---

## User Interface

### Dashboard Layout
- **KPI Cards** - 4 summary cards at top (Total Universe, Total Baseline, Total Accomplishment, Overall Progress)
- **Sector Cards** - 7 module cards with individual statistics
- **Navigation** - Links to module pages, user management, edit history
- **Search Bar** - Advanced search with filters

### Module Pages
- **Index Page** - Table view of all records with filtering
- **Create Page** - Form for new record entry
- **Edit Page** - Form for editing existing records
- **Show Page** - Detailed view of single record

### Color Scheme
- Modern gradient cards (blue, emerald, etc.)
- Color-coded PPA hierarchy levels
- Responsive design for mobile and desktop
- TailwindCSS styling

---

## System Configuration

### Environment Variables
- Database connection settings
- Application URL
- Mail configuration
- Session configuration
- Cache configuration

### Key Configuration Files
- `config/app.php` - Application settings
- `config/database.php` - Database configuration
- `config/auth.php` - Authentication configuration
- `.env` - Environment-specific settings

---

## Maintenance and Support

### Database Maintenance
- Migration system for schema changes
- Seeders for initial data
- JSON field indexing for performance
- Regular backup recommendations

### Performance Optimization
- Eager loading for relationships
- JSON field indexing
- Query optimization
- Caching strategies

### Logging
- Laravel's built-in logging
- Edit history logging
- Error tracking
- User action logging

---

## Future Enhancements

### Potential Improvements
- Export functionality (PDF, Excel)
- Advanced reporting and analytics
- Data visualization charts
- Mobile app development
- API rate limiting
- Two-factor authentication
- Email notifications
- Workflow approval system
- Data import/export tools

---

## Contact and Support

For system issues, feature requests, or questions:
- System Administrator: [Contact Information]
- Technical Support: [Contact Information]
- Documentation Updates: [Contact Information]

---

**Document Version:** 1.0  
**Last Updated:** June 18, 2026  
**System Version:** 3.0  
**Laravel Version:** Latest  
**PHP Version:** Latest
