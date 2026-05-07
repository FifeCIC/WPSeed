# Plugin Modernization Standard

## Purpose

This document defines the modernization baseline for legacy plugins being brought up to WPSeed standards. It is intended for plugin-level migrations where existing behavior and data must be preserved while architecture, safety, and maintainability are improved.

This is a standard for modernization work, not a requirement to copy WPSeed wholesale into every plugin.

## Core Principles

### Preserve Outcomes, Replace Fragile Internals

- Preserve the user-visible workflow where it still adds value.
- Preserve legacy data ownership and read paths until migrations are verified.
- Replace brittle internal architecture rather than preserving it for its own sake.

### Compatibility Before Clean Sweep

- New architecture may sit behind compatibility wrappers.
- Legacy options, tables, hooks, and classes can remain readable during transition.
- Avoid big-bang rewrites where one regression can strand existing users.

### Standards First

- Introduce namespaces for new code.
- Move toward predictable bootstrapping and loading.
- Add capability checks and nonce verification to every mutating action.
- Prefer prepared queries and WordPress APIs where practical.
- Escape output and sanitize input consistently.

## Required Modernization Targets

### 1. Bootstrap And Load Order

- The plugin entry file must be minimal and understandable.
- Load order must be documented.
- New bootstrap or loader code must be compatible with the legacy runtime during transition.
- Always-loaded, admin-only, and optional subsystems should be distinguishable.

### 2. Namespaces And Class Loading

- All new classes should use a plugin-specific namespace.
- Legacy global class names may be kept temporarily through compatibility layers.
- Do not mass-rename the entire codebase in the first migration slice.

### 3. Data Ownership And Migrations

- Existing options, custom tables, and post meta must be inventoried before structural changes.
- Every plugin must document which tables/options are legacy-but-supported and which become the target model.
- Migration code must be reversible or at least auditable.

### 4. Security And Safe Execution

- All writes require capability checks and nonces.
- SQL must be reviewed for preparation and escaping.
- Dangerous tools or destructive actions need explicit confirmation.
- Background or automatic execution must be visible and explainable.

### 5. Admin Architecture

- Large legacy admin systems should be broken into clearer screens or modules.
- Preserve strong legacy interaction patterns when they still serve users.
- Alternative interfaces are allowed and encouraged when they reduce complexity.

### 6. Install And Uninstall Discipline

- Activation, deactivation, and uninstall behavior must be explicit.
- Uninstall cleanup must document why direct database deletes are or are not used.
- Scheduled jobs, transients, options, and custom tables must be accounted for.

### 7. Documentation

- Each plugin needs a roadmap.
- Each plugin should have at least one current-state baseline document before deeper rewrites.
- Architecture notes should explain the migration path, not just the destination.

### 8. Validation

- Every modernization slice should end with the narrowest practical validation.
- Prefer activation checks, smoke tests, or focused syntax checks over broad unscoped validation.
- Do not proceed to adjacent edits before validating the first substantive change.

## Reusable WPSeed Patterns

These are good candidates to borrow or mirror across plugins:

- Guarded bootstrap with a clear main entry file.
- Separation of core, admin, and optional subsystems.
- Explicit install/uninstall lifecycle handling.
- Documentation standards and roadmap discipline.
- Optional background task patterns where long-running work exists.
- REST/controller separation where modern API surfaces are needed.

These should usually be treated as references, not copied blindly:

- Example/demo features.
- Ecosystem-specific integrations.
- Boilerplate subsystems unrelated to the plugin's actual product.

## UX Modernization Rule

Legacy workflows that provide real value should be preserved even when the implementation changes.

Examples:

- A useful wizard may remain as a primary guided path.
- A cleaner advanced interface may be added beside that wizard.
- A compatibility view may coexist with a new modular admin surface during migration.

The right question is not whether a legacy interface is old. The right question is whether it is still helping users accomplish the job.

## Recommended First Slice For Any Legacy Plugin

1. Document bootstrap and load order.
2. Document data ownership.
3. Introduce a compatibility-safe bootstrap or loader path.
4. Preserve the strongest product workflow.
5. Validate before widening scope.