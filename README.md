# Dynamic Properties System

Лабораторная работа: Система динамических свойств с типизированными ключами и централизованным реестром.

## Цель работы

Создать "библиотеку", которая позволяет:
1. Расширять сущности динамическими свойствами без изменения базового класса
2. Предотвращать конфликты имён между разными модулями
3. Обеспечивать type safety при работе с динамическими данными
4. Определять операции над сущностями на основе их свойств

## Структура проекта

```
dynamic-properties-system/
├── src/
│   ├── Core/                          
│   │   ├── PropertyKey.php            
│   │   ├── PropertyContainer.php      
│   │   ├── Entity.php                 
│   │   ├── EntityOperationInterface.php  
│   │   └── EntityProcessor.php        
│   └── Modules/                       
│       ├── User/                      
│       │   ├── UserPropertyKeys.php
│       │   └── Operations/
│       │       ├── CheckAdultOperation.php
│       │       ├── FormatUserInfoOperation.php
│       │       └── ApplyPremiumDiscountOperation.php
│       └── Product/                   
│           ├── ProductPropertyKeys.php
│           └── Operations/
│               ├── CalculateTotalPriceOperation.php
│               ├── CheckStockAvailabilityOperation.php
│               └── FormatProductInfoOperation.php
├── examples/
│   ├── 01_basic_usage.php            
│   ├── 02_user_module.php            
│   ├── 03_product_module.php         
│   ├── 04_key_conflicts.php          
│   └── 05_mixed_usage.php            
├── composer.json
└── README.md
```

## старт

### Установка

```bash
composer install
```

### Базовый пример

```php
<?php
require_once 'vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Modules\User\UserPropertyKeys;


$user = new Entity(1, "Ivan Petrov");


$user->setProperty(UserPropertyKeys::$EMAIL, "ivan@example.com");
$user->setProperty(UserPropertyKeys::$AGE, 25);
$user->setProperty(UserPropertyKeys::$IS_PREMIUM, true);

$userEmail = $user->getProperty(UserPropertyKeys::$EMAIL);
$userAge = $user->getProperty(UserPropertyKeys::$AGE);

echo "User: {$user->getName()}\n";
echo "Email: {$userEmail}\n";
echo "Age: {$userAge}\n";
```

## Ключевые концепции

### 1. PropertyKey - Типизированный ключ

```php
/**
 * @var PropertyKey<string>
 */
public static PropertyKey $EMAIL;

self::$EMAIL = PropertyKey::create('user.email');
```

**Преимущества:**
- Type safety (IDE знает тип значения)
- Защита от конфликтов (повторная регистрация выбросит исключение)
- Быстрый доступ (использует int ID внутри)

### 2. PropertyContainer - Контейнер свойств

Хранилище с двумя способами доступа:

```php
$container->set(UserPropertyKeys::$EMAIL, "user@example.com");
$email = $container->get(UserPropertyKeys::$EMAIL);

$container->setByName('phone', '+1234567890');
$phone = $container->getByName('phone');
```

### 3. Entity - Расширяемая сущность

```php
$entity = new Entity(1, "Entity Name");

$entity->getId();
$entity->getName();

$entity->setProperty($anyKey, $anyValue);
```

### 4. EntityOperation - Операции над сущностями

```php
class CheckAdultOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $context): mixed
    {
        $age = $entity->getProperty(UserPropertyKeys::$AGE);
        return $age >= 18;
    }
    
    public function getName(): string
    {
        return 'check_adult';
    }
}
```

## Примеры использования

Смотрите директорию `examples/`:

1. **01_basic_usage.php** - Основы работы с системой
2. **02_user_module.php** - Работа с пользователями и операциями
3. **03_product_module.php** - Работа с товарами
4. **04_key_conflicts.php** - Защита от конфликтов ключей
5. **05_mixed_usage.php** - Комбинирование разных подходов

Запуск примера:

```bash
php examples/01_basic_usage.php
```

##  Архитектурные решения

### Почему не FatStruct?

**FatStruct** - это подход, когда все возможные свойства добавляются как поля класса:

```php
class User {
    public int $id;
    public string $name;
    public ?string $email;
    public ?int $age;
    public ?string $phone;
    public ?bool $isPremium;
    // ... десятки полей для всех возможных случаев
}
```

**Проблемы для библиотек:**
1.  Библиотека не знает, какие поля понадобятся
2.  Нельзя добавить новые поля без изменения библиотеки
3.  Трата памяти на неиспользуемые поля
4.  Конфликты между модулями

### Почему словарь с типизированными ключами?

### Централизованный реестр

```php
// Первый модуль
$key1 = PropertyKey::create('user.email');

// Второй модуль пытается использовать то же имя
$key2 = PropertyKey::create('user.email'); 
```

Это предотвращает ситуацию, когда два модуля записывают разные данные по одному ключу.

## Выполнение заданий лабораторной

### Задание 1: Изучить примеры из библиотек
# Исследование: Динамические свойства в реальных библиотеках

## Введение

При создании библиотек возникает фундаментальная проблема: **библиотека не может знать заранее, какие данные понадобятся её пользователям**. Эта документация исследует, как крупные фреймворки решают эту проблему.

---

## 1. ASP.NET Core - AuthenticationProperties

### Контекст проблемы

Система аутентификации должна сохранять данные в cookie или JWT токене, но не может знать:
- Какие claims добавит приложение
- Какие метаданные понадобятся для сессии
- Какие custom данные нужны для business logic

### Решение

```csharp
public class AuthenticationProperties
{
    // Словарь для хранения произвольных данных
    public IDictionary<string, string> Items { get; }
    
    // Библиотека определяет свои известные ключи как константы
    internal const string IssuedUtcKey = ".issued";
    internal const string ExpiresUtcKey = ".expires";
    internal const string IsPersistentKey = ".persistent";
    
    // Пользователи могут добавлять свои данные
    // properties.Items["custom_data"] = "value";
}
```

### Ключевые особенности

1. **Строковый словарь** `IDictionary<string, string>`
    - Простота использования
    - Все значения сериализуются в string
    - Нужен parsing при чтении

2. **Внутренние константы** для предопределённых ключей
    - Библиотека использует префикс `.` для своих ключей
    - Снижает риск конфликтов с пользовательскими данными

3. **Свойства-обёртки** для удобства
   ```csharp
   public DateTimeOffset? ExpiresUtc 
   {
       get { 
           return ParseDate(Items[ExpiresUtcKey]); 
       }
       set { 
           Items[ExpiresUtcKey] = value.ToString(); 
       }
   }
   ```

### Плюсы и минусы

**Плюсы:**
- Очень простой API
- Легко сериализуется
- Понятен разработчикам

**Минусы:**
- Нет type safety (всё string)
- Риск конфликтов имён между модулями
- Нужен парсинг для сложных типов

---

## 2. ASP.NET Core - HttpContext.Items

### Контекст проблемы

Middleware pipeline: каждый middleware может добавлять данные, которые понадобятся следующим. Типы данных заранее неизвестны.

### Решение

```csharp
public abstract class HttpContext
{
    // Словарь для передачи данных между middleware
    public abstract IDictionary<object, object> Items { get; }
}

// Использование
public class Middleware1
{
    public async Task Invoke(HttpContext context)
    {
        var userId = GetUserId();
        context.Items["userId"] = userId;  // Сохраняем
        await _next(context);
    }
}

public class Middleware2
{
    public async Task Invoke(HttpContext context)
    {
        var userId = (int)context.Items["userId"];  // Читаем
        // ...
    }
}
```

### Ключевые особенности

1. **`IDictionary<object, object>`** - максимальная гибкость
    - Ключ может быть любым объектом
    - Значение может быть любым объектом
    - Часто используют строковые ключи для простоты

2. **Scope per request**
    - Каждый HTTP request получает свой экземпляр
    - Автоматически очищается после обработки запроса

3. **Нет защиты от конфликтов**
    - Два middleware могут случайно использовать один ключ
    - Решение: соглашения об именовании (prefixes)

### Плюсы и минусы

**Плюсы:**
- Максимальная гибкость
- Можно хранить объекты любых типов
- Не требует сериализации

**Минусы:**
- **Полное отсутствие type safety**
- Нужны небезопасные касты: `(int)context.Items["key"]`
- Риск runtime ошибок при неправильном типе
- Нет compile-time проверок

---


### Ключевые особенности

1. **Generic методы** для улучшения type safety
    - `GetGlobalState<T>()` - возвращает типизированное значение
    - Меньше кастов в пользовательском коде

2. **Разделение Global и Local состояния**
    - GlobalState - на весь запрос
    - ContextData - локально для resolver

3. **Всё ещё строковые ключи**
    - Но с generic возвратом

---

## Сравнительная таблица подходов
---

## Выводы для нашей реализации

### Что мы заимствуем

1. **От AuthenticationProperties:**
    - Идею обёрточных свойств для удобства
    - Использование констант для предопределённых ключей

2. **От HttpContext.Items:**
    - Гибкость словарной структуры
    - Поддержку любых типов данных

    
---

## Заключение

Решение всегда сводится к какой-то форме **словаря/мапы**, но с разными trade-offs между:
- Type safety
- Простотой использования
- Защитой от ошибок


### Задание 2: Объяснить проблему FatStruct
FatStruct (толстая структура) - это подход, когда все возможные поля добавляются в один класс:

```php
class FatEntity
{
    public int $id;
    public string $name;
    
    public ?string $email = null;
    public ?int $age = null;
    public ?string $phone = null;
    public ?bool $isPremium = null;
    public ?int $registrationDate = null;
    
    public ?float $price = null;
    public ?int $quantity = null;
    public ?string $category = null;
    public ?string $sku = null;
    public ?float $weight = null;
    
    public ?int $userId = null;
    public ?int $productId = null;
    public ?string $shippingAddress = null;
    public ?string $paymentMethod = null;
    
}
```

### Почему это плохо для библиотек?

#### 1. Библиотека не знает будущих требований

Библиотека НЕ МОЖЕТ предугадать:
- Какие модули будут созданы в будущем
- Какие свойства понадобятся пользователям
- Какие новые требования появятся в business logic

#### 2. Огромная трата памяти

```php
// FatStruct занимает место даже для неиспользуемых полей

$user = new FatEntity();
$user->email = "user@example.com";
$user->age = 25;

// Но в памяти также хранятся:
// $price = null         (8 bytes)
// $quantity = null      (8 bytes)
// $category = null      (8 bytes)
// $sku = null           (8 bytes)
// ... ещё 40+ полей
// 
// Итого: ~400+ bytes на сущность
```

#### 3. Невозможность расширения без изменения кода библиотеки  



#### 4. Конфликты между модулями 

```php
class FatEntity {
    // Модуль "User" хочет:
    public ?string $status;  // 'active', 'inactive'
    
    // Модуль "Order" тоже хочет:
    public ?string $status;  // 'pending', 'shipped'
    
}
```

#### 5. Обязательные проверки на null

```php

function calculateDiscount(FatEntity $entity): float
{
    if ($entity->price === null) {
        throw new Exception("Price not set");
    }
    
    if ($entity->isPremium === null) {
        throw new Exception("Premium status not set");
    }
    
    if ($entity->quantity === null) {
        throw new Exception("Quantity not set");
    }
    
    // Только теперь можем работать
    return $entity->price * $entity->quantity * ($entity->isPremium ? 0.9 : 1.0);
}

```

### Задание 3: Создать систему с динамическими свойствами
Реализовано в `src/Core/` - библиотека полностью изолирована от потребителей.

### Задание 4: Централизованный реестр ключей
Реализовано в `PropertyKey::create()` - выбрасывает исключение при дублировании.

### Type Safety в PHP

PHP не поддерживает настоящие generics, но можно PHPDoc для помощи IDE:

```php
/**
 * @template T
 */
class PropertyKey {
    /**
     * @template T
     * @return PropertyKey<T>
     */
    public static function create(string $name): self;
}

/**
 * @var PropertyKey<string>
 */
public static PropertyKey $EMAIL;
```


