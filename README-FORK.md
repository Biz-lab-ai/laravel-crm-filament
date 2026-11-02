# Fork copy of venturedrake/laravel-crm-filament

- Upstream: https://github.com/venturedrake/laravel-crm-filament
- Upstream commit: 5c6e31d1 (release 1.3.0), exported with `git archive 5c6e31d1` (export-ignore applied, same as the Packagist dist).
- Why a copy: upstream pins `spatie/laravel-permission: ^6.0`; this application requires `^8.3`.
  Composer resolves dependency constraints before any file patch can apply, so the only way to
  install is a package copy with a relaxed constraint (see /root/agents/nariady/plan-crm-venturedrake-13-10.md, decision 2).
- Changes against upstream:
  1. composer.json: `"spatie/laravel-permission": "^6.0|^7.0|^8.0"` and the added `"version": "1.3.0"` field
     (PathPackagesCarryVersionTest requires every path package to name its version).
  2. Documents without a persistent PDF (plan CRM 2.1h, crm-2-1h-2): `Support\CrmPdf::render()` returns the bytes from
     memory; `CrmPdf::download()`, `Concerns\DownloadsPdf::streamPdfDownload()`, the quote row action
     (`QuoteResource::downloadPdfActionFactory()`) and `Http\Controllers\DocumentPreviewController` stream them
     (`StreamedResponse`, no file). `renderToDisk()` stays for the senders that attach a file. Goes into the upstream PR.
  3. The document send hook (plan CRM 2.1h, crm-2-1h-3a): `Contracts\SendsDocuments`, named by class in
     `laravel-crm.document_sender`, resolved by `Support\DocumentSending::sender()`; null keeps `Support\MailDocumentSender`,
     the former body of `QuoteResource::dispatchQuoteSend()` / `HasQuoteSendAction::dispatchQuote()` moved unchanged. Both quote
     send actions call the hook; the invoice and purchase-order sends are untouched. Goes into the upstream PR.
  4. One renderer for every document PDF (plan CRM 2.1i, crm-2-1i-2): the contract `Contracts\RendersPdf`
     (render(view, data, paper, orientation): bytes), the upstream path `Support\DompdfViewRenderer` (the Pdf facade,
     fontDir = public_path) as the default of the new config key `laravel-crm-filament.pdf_renderer`, bound to
     `RendersPdf` by the provider; `Support\CrmPdf::render()`/`renderToDisk()` and `Support\PdfTemplatePreview::render()`
     resolve `app(RendersPdf::class)` and no longer touch the facade. The host binds its own renderer by config. Goes
     into the upstream PR.
  5. The panel speaks the reader's language (plan CRM 4.5, job crm-4-5): every hardcoded English caption of the
     plugin's Filament pages, resources, relation managers, widgets and blade views - field labels, section titles,
     page titles (`getTitle()`), navigation groups and labels (`modelLabel()`/`pluralModelLabel()`), kanban and
     activity-feed headers, empty-state and action texts - becomes `__('laravel-crm-filament::labels.…')`, and every
     date rendering goes through Carbon `translatedFormat()` in `app()->getLocale()` instead of `format()`. The new
     English source keys live in the fork's own `resources/lang/en/labels.php` (upstream ships the file without
     them); the ru/he dictionaries live in the wrapper shelf (`packages/bizai/crm-desk/resources/lang/vendor/`),
     pinned key-for-key by the shelf's coverage gate. Goes into the upstream PR.
  6. Catalogue names speak the reader's language (plan CRM 4.5(г); the wrapper shelf installs the name cast on every
     retrieved catalogue row): the option lists that read a name straight out of the database through `pluck()` now
     hydrate the records and read `->name`, so the cast applies - the stage select of `Leads\LeadResource::form()` and
     its `pipeline_stage_id` filter, the `pipeline_stage_id` filter of `Quotes\QuoteResource`, the stage select of
     `Concerns\Forms\SalesDetailsSection` and the address type select of `Pages\GeneralSettings`. The two `labels`
     filters get `getOptionLabelFromRecordUsing(fn (Model $record) => $record->name)` for the same reason. Goes into
     the upstream PR together with the shelf's cast.
  7. The calendar speaks the reader's language (plan CRM 4.5, gate 4): `views/calendar/index.blade.php` passes
     `locale: '{{ app()->getLocale() }}'` to `new FullCalendar.Calendar()` and loads FullCalendar's own locale pack
     (`@fullcalendar/core@6.1.15/locales/<locale>.global.min.js`) after the global bundle - the bundle ships no
     packs, and `Intl` alone covers only the month and weekday names, not the toolbar buttons and not the hebrew
     `direction: rtl` (`fc-direction-rtl`). Those texts no PHP render ever holds are measured only by the browser
     probe `tests/Browser/CrmCalendar`. The app locale itself is the core's `SetLocale` middleware work, untouched
     here. Goes into the upstream PR.
  8. The kanban boards survive a panel without the settings screens (plan CRM 4.5b): the empty state of
     `views/leads/kanban.blade.php` and `views/features/kanban.blade.php` links to the settings resource only
     when `Route::has()` knows its route; a panel that registers no settings (the wrapper shelf) shows the text
     without the link instead of failing. Goes into the upstream PR.
- Known upstream semgrep debt, marked in place: 10 lines of `src/` (empty catch) carry a same-line
  `// nosemgrep: semgrep.rules.php-empty-catch -- upstream 1.3.0 debt, README-FORK`. The rules block only new findings against the PR base, and the base of the
  import PR has no upstream code, so every upstream line counted as new. The marker silences one rule on one line;
  the rest of the copy stays scanned. List: `grep -rn "nosemgrep" forks/venturedrake/laravel-crm-filament/src`.
- Do not patch anything else here without a line above. Behaviour changes live in packages/bizai/crm-desk (the
  wrapper shelf).
- Upgrading: `git archive <new sha>` from upstream into this directory, re-apply the one-line
  constraint change and the version field, update this file, verify with `diff -r` against a fresh export.
- Pending upstream PR: relax the spatie constraint (plan item 0.2, after the owner's word).
- Not auto-discovered: both vendor providers are listed in the root composer.json under
  `extra.laravel.dont-discover`. With the packages installed but the wrapper shelf absent the
  application stays inert (no `laravel-crm` config, no `crm/api/v2` routes, no migrations loaded).
  The wrapper shelf `packages/bizai/crm-desk` registers the two providers explicitly after it has set
  the `laravel-crm.*` config it needs (teams, host team model, modules, user_interface=false).

## Exact file list against upstream v1.3.0

Every path of `git diff v1.3.0..bizai-patches` besides this file (A added, M modified, D deleted). The items above give the reasons.

```
M composer.json
M config/package.php
M resources/lang/en/labels.php
M resources/views/activity-feed.blade.php
M resources/views/calendar/index.blade.php
M resources/views/calls/kanban.blade.php
M resources/views/chat-widgets/embed.blade.php
M resources/views/clusters/settings/pages/reminders.blade.php
M resources/views/crm-activity.blade.php
M resources/views/crm-calls.blade.php
M resources/views/crm-files.blade.php
M resources/views/crm-lunches.blade.php
M resources/views/crm-meetings.blade.php
M resources/views/crm-notes.blade.php
M resources/views/crm-tasks.blade.php
M resources/views/deals/kanban.blade.php
M resources/views/features/kanban.blade.php
M resources/views/leads/kanban.blade.php
M resources/views/partials/crm-card-call.blade.php
M resources/views/partials/crm-card-lunch.blade.php
M resources/views/partials/crm-card-meeting.blade.php
M resources/views/partials/crm-card-note.blade.php
M resources/views/partials/crm-card-task.blade.php
M resources/views/quotes/kanban.blade.php
M resources/views/tasks/kanban.blade.php
M src/Auth/Profile.php
M src/Concerns/ContactFieldsSchema.php
M src/Concerns/DownloadsPdf.php
M src/Concerns/ExportsCsv.php
M src/Concerns/Forms/LeadDealContactSection.php
M src/Concerns/Forms/LineItemsRepeater.php
M src/Concerns/Forms/SalesDetailsSection.php
M src/Concerns/HasEncryptedGlobalSearch.php
M src/Concerns/HasXeroSyncStateInfolist.php
M src/Concerns/ImportsCsv.php
M src/Console/InstallCommand.php
A src/Contracts/RendersPdf.php
A src/Contracts/SendsDocuments.php
M src/Http/Controllers/DocumentPreviewController.php
M src/Jobs/SendUserInvite.php
M src/LaravelCrmFilamentServiceProvider.php
M src/Pages/ActivityFeed.php
M src/Pages/CalendarPage.php
M src/Pages/Dashboard.php
M src/Pages/GeneralSettings.php
M src/Pages/Reminders.php
M src/Pages/TemplateSettings.php
M src/Pages/Updates.php
M src/RelationManagers/CallsRelationManager.php
M src/RelationManagers/CrmCallsRelationManager.php
M src/RelationManagers/CrmFilesRelationManager.php
M src/RelationManagers/CrmLunchesRelationManager.php
M src/RelationManagers/CrmMeetingsRelationManager.php
M src/RelationManagers/CrmNotesRelationManager.php
M src/RelationManagers/CrmTasksRelationManager.php
M src/RelationManagers/FilesRelationManager.php
M src/RelationManagers/MeetingsRelationManager.php
M src/RelationManagers/NotesRelationManager.php
M src/RelationManagers/RelatedOrganizationsRelationManager.php
M src/RelationManagers/RelatedPeopleRelationManager.php
M src/RelationManagers/TasksRelationManager.php
M src/Resources/Chat/Pages/ViewChatConversation.php
M src/Resources/Deals/DealResource.php
M src/Resources/Deals/Pages/EditDeal.php
M src/Resources/Deals/Pages/ViewDeal.php
M src/Resources/Leads/LeadResource.php
M src/Resources/Leads/Pages/EditLead.php
M src/Resources/Leads/Pages/LeadKanban.php
M src/Resources/Leads/Pages/ViewLead.php
M src/Resources/Organizations/OrganizationResource.php
M src/Resources/Organizations/Pages/EditOrganization.php
M src/Resources/Organizations/Pages/ViewOrganization.php
M src/Resources/People/Pages/EditPerson.php
M src/Resources/People/Pages/ViewPerson.php
M src/Resources/People/PersonResource.php
M src/Resources/Quotes/Pages/Concerns/HasQuoteSendAction.php
M src/Resources/Quotes/Pages/EditQuote.php
M src/Resources/Quotes/Pages/QuoteKanban.php
M src/Resources/Quotes/Pages/ViewQuote.php
M src/Resources/Quotes/QuoteResource.php
M src/Resources/Tasks/Pages/ListTasks.php
M src/Resources/Tasks/Pages/TaskKanban.php
M src/Resources/Tasks/TaskResource.php
M src/Resources/Teams/Pages/ViewCrmTeam.php
M src/Resources/Users/Pages/Concerns/HasUserInviteAction.php
M src/Resources/Users/Pages/ViewUser.php
M src/Support/CrmPdf.php
A src/Support/DocumentSending.php
A src/Support/DompdfViewRenderer.php
M src/Support/InvitationUrl.php
A src/Support/MailDocumentSender.php
M src/Support/PdfTemplatePreview.php
M src/Widgets/LeadsByStageChart.php
M src/Widgets/LeadsVsDealsChart.php
M src/Widgets/RecentActivityList.php
M src/Widgets/TasksDueTodayList.php
```
