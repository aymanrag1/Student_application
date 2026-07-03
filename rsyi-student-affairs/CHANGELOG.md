# Changelog

## [0.1.0] - Initial Release

### Phase 0 - Scaffolding ✅
- Plugin main file with WordPress plugin header
- PSR-like autoloader for `RSYI_*` classes
- Singleton bootstrap (`RSYI_Plugin`)
- Activator: create uploads dir + `.htaccess` + schedule cron
- Deactivator: clear cron
- Uninstall handler: drops tables, deletes options, removes roles
- `readme.txt`, `composer.json`

### Phase 1 - Database Schema ✅
- `dbDelta` installer for 8 custom tables:
  - `rsyi_students` (main student records)
  - `rsyi_documents` (uploaded files)
  - `rsyi_status_log` (state transition audit)
  - `rsyi_interviews` (interview schedule + totals)
  - `rsyi_committee_votes` (per-member votes)
  - `rsyi_medical_exams` (6-element medical + overall result)
  - `rsyi_language_tests` (level + passed flag)
  - `rsyi_notifications` (all sent messages log)
- Constants class with all enums + Arabic labels + committee weight tables
- Student model + StatusLog model

### Phase 2 - Stage 1 Application Form ✅
- Shortcode `[rsyi_application_form]` renders 4-section form
- Personal / Contact / General / Education & Military
- Nonce protection, redirect with result codes
- Client JS toggles شعبة field for Egyptian/Azhari schools

### Phase 3 - Auto-Eligibility Filter ✅
- Egyptian nationality, age 21-29, male gender
- Accepted high school types + scientific track (for Egyptian schools)
- Military status validation
- Returns rejection codes + Arabic reasons

### Phase 4 - Stage 2 Document Upload ✅
- Document model with required/optional types
- Upload service: MIME validation, 5MB limit, safe filenames
- Files stored in `wp-content/uploads/rsyi-docs/{student_id}/`
- Auto-transition to `documents_uploaded` when all required uploaded

### Phase 5-6 - Admin Dashboard ✅
- Candidates list with filters + search + pagination
- Status badges color-coded per pipeline stage
- Candidate detail: profile grid + status transition form + history table
- Toggle reminder + manual "docs received" override

### Phase 7-8 - Interviews & Committee ✅
- Interview model (schedule, attendance, totals)
- CommitteeVote model (upsert per member)
- Committee service: weighted percentage tallies + final decision
  - 4-member: 25/25/25/25 accept, 15/15/15/15 waiting
  - 3-member: 40/30/30 accept, 20/15/15 waiting
  - Threshold: ≥60% accept → accepted, ≥30% total → waiting
- Interviews admin page with schedule form + committee panel per row

### Phase 9-10 - Medical & Language ✅
- Medical exam (6 elements): internal, chest, eye, toxicology, virology, blood
- Auto-computes overall (unfit if any element unfit)
- Language test (5 levels), accept ≥ elementary
- Auto-transitions: medical_passed + language_passed → final_accepted

### Phase 11-13 - Notifications & Cron ✅
- WhatsApp driver pattern (Log / UltraMsg / Meta Cloud API)
- Notification service subscribes to plugin events
- 8 message types in Arabic
- WP-Cron daily job with 4-day interval reminder logic
- Egyptian phone number normalization

### Phase 14 - Exports ✅
- CSV export: candidates list with status filter
- CSV export: security access list (day-before, filtered by date)
- UTF-8 BOM for Excel compatibility

### Phase 15 - REST API ✅
- Namespace `rsyi/v1`
- Bearer token auth (timing-safe)
- Endpoints: /students, /students/{id}, /students/{id}/documents, /statistics

### Phase 16 - Roles & Capabilities ✅
- Custom roles: rsyi_admin, rsyi_committee, rsyi_medical, rsyi_security
- Custom capabilities registered

### Phase 17 - Localization
- All UI strings wrapped in `__()` / `_e()` using text domain `rsyi-student-affairs`
- RTL CSS auto-loaded when Arabic locale active
- (Future: generate .pot file via WP-CLI)

### Phase 18-20 - Hardening / Testing / Packaging
- Nonces on all admin forms
- `current_user_can()` checks on all admin handlers
- Prepared statements throughout
- File upload MIME whitelist + size limit
- Uploads dir protected by `.htaccess`
- `INSTALL.md` with setup + configuration guide
