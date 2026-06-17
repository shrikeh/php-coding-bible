# Agent Instructions

This repository contains the PHP Coding Bible — a set of principles and practices for writing maintainable PHP. Read `README.md` in full before making any changes to it.

## What this repository is

A human-readable guide for PHP engineers. The `README.md` is the single source of truth. This file exists to help agents work on the document itself, and to provide a machine-readable summary of its rules for agents in other projects that adopt these standards.

Reusable agent skills live in [`skills/`](skills/). To execute a skill, read the relevant file and follow its steps.

- [`skills/php-coding-standards.md`](skills/php-coding-standards.md) — distilled PHP coding directives; load when writing or reviewing PHP code in any project following these standards

## Maintaining the document

When editing the README:

- **Preserve the author's voice.** The document is direct, occasionally sardonic, and uses concrete before/after code examples. The humour and narrative in sections like "Prefer class constants" are doing real work — they make principles memorable and the document worth reading. Do not strip them for the sake of brevity.
- **Keep the reasoning.** Each principle explains *why*, not just *what*. That reasoning is what makes the document persuasive to a sceptical reader. A rule without its rationale is just an instruction to be ignored.
- **Distinguish heuristics from laws.** Where genuine exceptions exist, say so: "strongly prefer", "by default", "as a rule". Presenting a heuristic as an absolute invites the counterexample that discredits the whole section.
- **Keep the links.** All external references (PSR-12, SOLID, Law of Demeter, the Wither pattern, the Uncle Bob talk) belong in the reference section at the bottom. They provide depth for readers who want it.
- **Code examples must model the principles.** All examples should use `final readonly` classes, named constructors, typed properties, and the other patterns the document advocates. An example that contradicts the rule it illustrates is worse than no example.

When evaluating a proposed change, ask:
- Does it add a genuine, well-reasoned caveat — or does it retreat from a defensible position?
- Is there a concrete before/after example that makes the principle immediate?
- Is the voice consistent with the surrounding document?
- Does the example code follow the other rules in the document?

## Distilled directives

A machine-readable summary for agents writing or reviewing PHP in projects that follow these standards. The `README.md` is authoritative; update this section when the README changes.

### Declarations

- `declare(strict_types=1)` on every PHP file, no exceptions.
- Type all parameters and return values.

### Classes

- `final readonly` by default. Relax `final` only when inheritance is genuinely required; relax `readonly` only when mutable state is unavoidable.
- Private constructor + named static constructors for distinct instantiation scenarios. The constructor validates only; named constructors normalise.
- Self-validate on construction — if an invariant is violated, throw a specific named exception immediately. An object in an invalid state must be impossible to construct.
- Maximum three arguments per method or constructor. This is a heuristic: apply judgement rather than creating artificial parameter objects.

### Properties and accessors

- No getters for simple value return. Use `public readonly` properties or `final readonly class` instead.
- When a method is needed (to match an interface, or because the value is computed or fetched), name it without the `get` prefix: `address()` not `getAddress()`.
- No mutation: return new instances via the wither pattern — `withBar(string $bar): self { return new self($bar); }`.

### Dependencies and construction

- Never instantiate collaborators with `new` inside business logic. Receive them via constructor injection.
- Factories create complex objects and implement an interface.
- Only three things call `new`: an object creating a new instance of itself, a dedicated factory, or a DI container.

### Failure handling

- A method either succeeds or throws a specific, named exception.
- Always wrap collaborator exceptions: `throw SpecificException::create($context, $previousException)`.
- Expected failure paths — record not found, payment declined, empty result — use union return types (`CustomerDetails|null`) or a result value object. These are not exceptional; do not throw for them.

### Comments

- Never comment *what* — if the code cannot speak for itself, fix the code.
- Comment *why* when the reason is non-obvious: a regulatory constraint, a third-party library workaround, a non-obvious invariant that a future reader might inadvertently break.

### General

- Composition over inheritance. `final` everywhere possible; use collaborators instead of subclasses.
- Class constants over magic numbers. Enums over multiple constants of the same type.
- Private by default. Expose the minimum public surface needed to fulfil the contract.
- Treat tests as first-class code: the same linting, type-checking, and quality standards apply as to application code.
