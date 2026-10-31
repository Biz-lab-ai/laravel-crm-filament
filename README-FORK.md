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
