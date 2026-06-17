# Skill: php-coding-standards

Distilled directives from the PHP Coding Bible. This file is intended to be
machine-readable by AI agents. For the full reasoning behind each rule, see
`README.md` — the human-readable document is authoritative; this is a
quick-reference summary for agents writing or reviewing PHP code.

When installed as a Composer package, this file is available at:
`vendor/shrikeh/php-coding-bible/skills/php-coding-standards.md`

---

## Declarations

- `declare(strict_types=1)` on every PHP file, no exceptions.
- Type all parameters and return values.

## Classes

- `final readonly` by default.
  Relax `final` only when inheritance is genuinely required.
  Relax `readonly` only when mutable state is unavoidable.
- Private constructor + named static constructors for distinct instantiation
  scenarios. The constructor validates only; named constructors normalise.
  Do not put normalisation logic in the constructor.
- Self-validate on construction. If an invariant is violated, throw a specific
  named exception immediately. An object in an invalid state must be impossible
  to construct.
- Maximum three arguments per method or constructor.
  This is a heuristic, not a law — apply judgement rather than creating
  artificial parameter objects to satisfy a count.

## Properties and accessors

- No getters for simple value return.
  Use `public readonly` properties or `final readonly class` instead.
- When a method is needed (to match an interface, or because the value is
  computed or fetched), omit the `get` prefix: `address()` not `getAddress()`.
  From the caller's perspective it is irrelevant whether the value is stored,
  calculated, or fetched — `get` adds nothing.
- No mutation. Return new instances via the wither pattern:
  `withBar(string $bar): self { return new self($bar); }`

## Dependencies and construction

- Never instantiate collaborators with `new` inside business logic.
  Receive them via constructor injection.
- Factories create complex objects and implement an interface.
- Only three things call `new`: an object creating a new instance of itself,
  a dedicated factory, or a DI container.

## Failure handling

- A method either succeeds or throws a specific, named exception.
- Always wrap collaborator exceptions:
  `throw SpecificException::create($context, $previousException)`
- Expected failure paths — record not found, payment declined, empty result —
  use union return types (`CustomerDetails|null`) or a result value object.
  These are not exceptional; do not throw for them.
  Reserve exceptions for things that genuinely should not happen.

## Comments

- Never comment *what* — if the code cannot speak for itself, fix the code.
- Comment *why* when the reason is non-obvious: a regulatory constraint,
  a third-party library workaround, a non-obvious invariant that a future
  reader might inadvertently violate. This context cannot be captured by
  naming alone, and deleting it is how the same problem gets rediscovered
  two years later.

## General

- Composition over inheritance. `final` everywhere possible;
  use collaborators instead of subclasses.
- Class constants over magic numbers.
  Enums over multiple constants of the same type.
- Private by default. Expose the minimum public surface needed
  to fulfil the contract.
- Tests are first-class code. Apply the same linting, type-checking,
  and quality standards to tests as to application code.
