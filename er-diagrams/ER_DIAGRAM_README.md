# ER-Diagram Documentation for Helprr Database

## Overview
This folder contains ER (Entity-Relationship) diagrams for the Helprr marketplace database in multiple formats.

---

## 📊 Database Overview

**Total Tables**: 7  
**Total Relationships**: 7  
**Total Constraints**: Multiple (FK, UK, PK, CHECK)

---

## 📁 Files in This Folder

### 1. **helprr_erd.mermaid** (Recommended for documentation)
- **Format**: Mermaid diagram
- **Best For**: GitHub README, Markdown documentation
- **Tool**: Use in GitHub markdown or [Mermaid Live Editor](https://mermaid.live)
- **Usage**: Embed directly in `.md` files using:
  ```markdown
  ```mermaid
  [paste content here]
  ```
  ```

### 2. **helprr_erd.plantuml** (For detailed diagrams)
- **Format**: PlantUML
- **Best For**: Professional documentation
- **Tool**: Use on [PlantUML Online Editor](http://www.plantuml.com/plantuml/uml/)
- **Software**: Can use in VS Code with PlantUML extension
- **Features**: More customization and styling options

### 3. **helprr_erd.dbdiagram** (Interactive visualization)
- **Format**: DBDiagram.io format
- **Best For**: Interactive exploration, SQL export
- **Tool**: Copy/paste to [DBDiagram.io](https://dbdiagram.io)
- **Features**: 
  - Drag-and-drop relationships
  - SQL export
  - Real-time visualization

### 4. **helprr_erd.dot** (For Graphviz)
- **Format**: Graphviz DOT format
- **Best For**: Graph visualization
- **Tool**: Use with [GraphViz Online](https://dreampuf.github.io/GraphvizOnline/)
- **Software**: Graphviz desktop application
- **Command**: `dot -Tpng helprr_erd.dot -o helprr_erd.png`

### 5. **ER_DIAGRAM_README.md** (This file)
- **Format**: Documentation
- **Purpose**: Guide to using the ER diagrams

---

## 🔗 Entity Relationships

### 1. **users** (Primary User Table)
**Role**: Stores all user accounts (customers, service providers, admins)

```
users
├── 1:M → service_providers (One user has many shops)
├── 1:M → reviews (One user writes many reviews)
├── 1:M → uploaded_files (One user uploads many files)
└── 1:M → sessions (One user has many sessions)
```

**Key Attributes**:
- PK: `id`
- UK: `email` (Unique email)
- Role: 'user' | 'shop' | 'admin'

---

### 2. **service_providers** (Shop Profiles)
**Role**: Stores service provider/technician information

```
service_providers
├── FK → users (Many:1 - Must have a user)
├── 1:M → services_offered (One shop offers many services)
└── 1:M → reviews (One shop receives many reviews)
```

**Key Attributes**:
- PK: `id`
- FK: `user_id` (Links to users)
- Status: `approved` (Boolean for admin approval)
- Flexible: `category_ids` (JSON array)

---

### 3. **services_offered** (Individual Services)
**Role**: Lists specific services a provider offers

```
services_offered
└── FK → service_providers (Many:1 - Each service belongs to one provider)
```

**Key Attributes**:
- PK: `id`
- FK: `provider_id`
- Pricing: `price`, `estimated_time`

---

### 4. **reviews** (Customer Reviews & Ratings)
**Role**: Stores customer feedback and ratings

```
reviews
├── FK → users (Many:1 - Review written by user)
└── FK → service_providers (Many:1 - Review about provider)
```

**Key Attributes**:
- PK: `id`
- FK: `user_id`, `provider_id`
- Constraint: `rating BETWEEN 1 AND 5`

---

### 5. **electronics_categories** (Service Categories)
**Role**: Defines types of electronics that can be repaired

```
electronics_categories
└── Referenced by service_providers via `category_ids` (JSON)
```

**Key Attributes**:
- PK: `id`
- UK: `name` (Unique category name)
- 8 Pre-loaded: Laptop, TV, AC, Geyser, Mobile, Fridge, Washing Machine, Microwave

---

### 6. **uploaded_files** (File Storage Tracking)
**Role**: Tracks uploaded certificates and images

```
uploaded_files
└── FK → users (Many:1 - Files uploaded by user)
```

**Key Attributes**:
- PK: `id`
- FK: `user_id`
- Type: 'certificate' | 'image' | 'document'

---

### 7. **sessions** (Session Management)
**Role**: Tracks active user sessions

```
sessions
└── FK → users (Many:1 - Session belongs to user)
```

**Key Attributes**:
- PK: `session_id` (VARCHAR 128)
- FK: `user_id`
- IP tracking: `ip_address`

---

## 📈 Relationship Types

| Relationship | From | To | Type | Example |
|------------|------|-----|------|---------|
| User → Shop | users | service_providers | 1:M | One user can have one shop |
| User → Review | users | reviews | 1:M | One user writes multiple reviews |
| User → Files | users | uploaded_files | 1:M | One user uploads many files |
| User → Sessions | users | sessions | 1:M | One user has many sessions |
| Shop → Services | service_providers | services_offered | 1:M | One shop offers many services |
| Shop → Reviews | service_providers | reviews | 1:M | One shop gets many reviews |

---

## 🔑 Key Constraints

### Primary Keys (PK)
- Every table has a primary key for unique identification
- Most use `INT AUTO_INCREMENT` for easier management
- Sessions use `VARCHAR(128)` for session ID

### Foreign Keys (FK)
- All relationships use CASCADE delete for data integrity
- When parent deleted, child records are automatically deleted
- Prevents orphaned data

### Unique Keys (UK)
- `users.email` - Only one account per email
- `electronics_categories.name` - Unique category names

### Check Constraints
- `reviews.rating` - Must be between 1 and 5
- `role` - Limited to 'user', 'shop', 'admin'

---

## 🎯 How to Use These Diagrams

### For Documentation
1. Open [Mermaid Live Editor](https://mermaid.live)
2. Copy content from `helprr_erd.mermaid`
3. Embed in your README or documentation

### For Visual Design
1. Go to [DBDiagram.io](https://dbdiagram.io)
2. Copy content from `helprr_erd.dbdiagram`
3. Explore relationships interactively

### For Presentations
1. Use PlantUML Editor: [PlantUML Online](http://www.plantuml.com/plantuml/uml/)
2. Copy content from `helprr_erd.plantuml`
3. Export as PNG/SVG for slides

### For Graph Visualization
1. Use GraphViz Online: [GraphViz Online](https://dreampuf.github.io/GraphvizOnline/)
2. Copy content from `helprr_erd.dot`
3. Generate visual diagram

---

## 🔍 Query Examples Based on ER Diagram

### Get shop with all details and reviews
```sql
SELECT sp.*, u.name, AVG(r.rating) as avg_rating
FROM service_providers sp
JOIN users u ON sp.user_id = u.id
LEFT JOIN reviews r ON sp.id = r.provider_id
GROUP BY sp.id;
```

### Get user's services and reviews
```sql
SELECT 
    so.title,
    so.price,
    COUNT(r.id) as review_count,
    AVG(r.rating) as avg_rating
FROM services_offered so
LEFT JOIN reviews r ON so.provider_id = r.provider_id
WHERE so.provider_id = ?
GROUP BY so.id;
```

### Get reviews written by user
```sql
SELECT r.*, sp.shop_name, u.name as reviewer_name
FROM reviews r
JOIN service_providers sp ON r.provider_id = sp.id
JOIN users u ON r.user_id = u.id
WHERE r.user_id = ?
ORDER BY r.created_at DESC;
```

---

## 📐 Database Normalization

The schema follows **Third Normal Form (3NF)**:

✅ **1NF** (Atomic values)
- All columns contain atomic (indivisible) values
- Exception: `category_ids` (JSON) for flexibility

✅ **2NF** (No partial dependencies)
- All non-key attributes depend on entire primary key
- Foreign keys properly established

✅ **3NF** (No transitive dependencies)
- All non-key attributes depend only on primary key
- No indirect relationships through other attributes

---

## 🚀 Using Generated SQL from DBDiagram

1. Create tables at [DBDiagram.io](https://dbdiagram.io)
2. Use "Export" → "Export to SQL"
3. Copy generated SQL
4. Modify if needed
5. Import to your MySQL database

---

## 📝 Index Strategy (For Performance)

**Indexes Created**:
```sql
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_provider_user_id ON service_providers(user_id);
CREATE INDEX idx_services_provider_id ON services_offered(provider_id);
CREATE INDEX idx_reviews_provider_id ON reviews(provider_id);
CREATE INDEX idx_reviews_user_id ON reviews(user_id);
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
```

These optimize common queries:
- User lookup by email
- Get shop by user
- Get services by provider
- Get reviews for provider
- Session lookups

---

## 📞 References

- **DBDiagram**: https://dbdiagram.io
- **Mermaid**: https://mermaid.live
- **PlantUML**: http://www.plantuml.com/
- **GraphViz**: https://graphviz.org/
- **MySQL Documentation**: https://dev.mysql.com/doc/

---

**Last Updated**: January 2026  
**Database Version**: 1.0.0  
**Format**: Multiple (Mermaid, PlantUML, DBDiagram, GraphViz)
