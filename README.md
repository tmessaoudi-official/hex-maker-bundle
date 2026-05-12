# HexMakerBundle

Symfony MakerBundle extension that scaffolds **Hexagonal Architecture + CQRS** boilerplate in 10 seconds.

> **Scaffolding, not magic.** Generated code is plain PHP — no runtime dependency after generation. Install in dev only, uninstall safely once your scaffold is done.

---

## Installation

```bash
composer require --dev takitech/hex-maker-bundle
```

Register the bundle in `config/bundles.php` (Symfony Flex does this automatically):

```php
return [
    // ...
    TakiTech\HexMakerBundle\HexMakerBundle::class => ['dev' => true],
];
```

---

## Commands

### `make:domain:entity` — Scaffold a domain entity

```bash
php bin/console make:domain:entity Order
```

**Interactive prompts:**

```
Add value object for ID (OrderId)? [Y/n]: Y
Additional value objects? Enter comma-separated field names: email,status
Add repository interface (OrderRepositoryInterface)? [Y/n]: Y
Add domain exception (OrderNotFoundException)? [Y/n]: Y
```

**Files generated:**

```
src/
└── Domain/
    ├── Exception/
    │   └── OrderNotFoundException.php
    ├── Model/
    │   ├── Email.php           ← value object
    │   ├── Order.php           ← aggregate root
    │   ├── OrderId.php         ← UUID value object
    │   └── Status.php          ← value object
    └── Repository/
        └── OrderRepositoryInterface.php
```

---

### `make:cqrs:command` — Scaffold a CQRS command pair

```bash
php bin/console make:cqrs:command CreateOrder
```

**Interactive prompts:**

```
Which entity does this command act on? (leave blank to skip import): Order
Return type of handler? [void]:
```

**Files generated:**

```
src/Application/Command/CreateOrder/
├── CreateOrderCommand.php
└── CreateOrderCommandHandler.php

tests/Unit/Application/Command/
└── CreateOrderCommandHandlerTest.php
```

---

### `make:cqrs:query` — Scaffold a CQRS query + DTO

```bash
php bin/console make:cqrs:query GetOrderById
```

**Interactive prompts:**

```
Output DTO class name? [OrderByIdOutput]:
```

**Files generated:**

```
src/Application/
├── DTO/Output/
│   └── OrderByIdOutput.php
└── Query/GetOrderById/
    ├── GetOrderByIdQuery.php
    └── GetOrderByIdQueryHandler.php

tests/Unit/Application/Query/
└── GetOrderByIdQueryHandlerTest.php
```

---

### `make:domain:event` — Scaffold a domain event

```bash
php bin/console make:domain:event OrderCreated
```

**Files generated:**

```
src/Domain/Event/
└── OrderCreated.php   ← PHP 8.4 readonly class with occurredAt timestamp
```

---

## Why generated code over runtime abstractions?

**Generated code is yours to own.** Once scaffolded:

- Files live in your `src/` — your IDE understands them, your team reads them
- No magic at runtime — no proxy classes, no hidden metadata, no reflection tricks
- Full debuggability — step through a command handler in Xdebug like any other class
- No update risk — upgrading this bundle never breaks existing generated code
- Uninstall cleanly — remove the bundle after scaffolding, zero production footprint

Runtime abstractions hide complexity and create "framework lock-in without the framework". Generated scaffolding creates clarity.

---

## Example: Full Order feature in one session

```bash
# 1. Domain layer
php bin/console make:domain:entity Order
# → Order, OrderId, OrderRepositoryInterface, OrderNotFoundException

# 2. Create command
php bin/console make:cqrs:command CreateOrder
# → CreateOrderCommand, CreateOrderCommandHandler, CreateOrderCommandHandlerTest

# 3. Query
php bin/console make:cqrs:query GetOrderById
# → GetOrderByIdQuery, GetOrderByIdQueryHandler, OrderByIdOutput, GetOrderByIdQueryHandlerTest

# 4. Domain event
php bin/console make:domain:event OrderCreated
# → OrderCreated

# 5. Wire infrastructure (manual — edit the generated interface)
#    Create src/Infrastructure/Persistence/Doctrine/DoctrineOrderRepository.php
```

Total time from zero to all boilerplate: **under 2 minutes**.

---

## Generated file samples

### `Order.php` (domain entity)

```php
final class Order
{
    public function __construct(
        private readonly OrderId $id,
    ) {}

    public function id(): OrderId
    {
        return $this->id;
    }

    // Add domain behaviour here
}
```

### `OrderId.php` (UUID value object)

```php
final readonly class OrderId
{
    public static function generate(): self { ... }
    public static function fromString(string $value): self { ... }
    public function equals(self $other): bool { ... }
}
```

### `CreateOrderCommandHandler.php`

```php
#[AsMessageHandler]
final readonly class CreateOrderCommandHandler
{
    public function __construct(
        // private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(CreateOrderCommand $command): void
    {
        // Implement: load → mutate → persist
    }
}
```

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | ^8.4 |
| Symfony Framework Bundle | ^7.0 |
| Symfony Maker Bundle | ^1.58 |

---

## License

MIT — see [LICENSE](LICENSE).
