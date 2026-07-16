> Этот сервис: `api-placemarkers-database` является частью приложения [placemarkers-demo-workstation](https://github.com/Vlad812/placemarkers-demo-workstation).

# API Documentation: Placemarkers Database

**Стек технологий:**

- PHP 8.5
- Symfony 8
- RoadRunner
- PostgreSQL
- Doctrine ORM

Сервис записи (CQRS Write Model) для гео-меток и тегов. Отвечает за создание, обновление и удаление данных; все изменения сохраняются в **Primary** PostgreSQL через **Doctrine ORM** (сущности, репозитории, миграции).

---
## Создает новую пользовательскую метку на карте с заданными координатами, названием и опциональными атрибутами (описание, тип, теги).

**URL:** `/api/placemarkers`  
**Метод:** `POST`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)

- `Content-Type: application/json`
- `Authorization: Bearer <token>`

#### Тело запроса (JSON Body)

| Поле          | Тип             | Обязательное | Описание                                                  | Пример                        |
| ------------- | --------------- | ------------ | --------------------------------------------------------- | ----------------------------- |
| `name`        | `string`        | **Да**       | Название метки.                                           | `"Любимая кофейня"`           |
| `lat`         | `number`        | **Да**       | Широта (Latitude).                                        | `55.755826`                   |
| `lon`         | `number`        | **Да**       | Долгота (Longitude).                                      | `37.617299`                   |
| `description` | `string`        | Нет          | Подробное описание метки. По умолчанию пустая строка.     | `"Отличный кофе и круассаны"` |
| `type_id`     | `string`        | Нет          | Идентификатор типа метки. По умолчанию `default`.         | `"cafe"`                      |
| `tags`        | `array<string>` | Нет          | Массив идентификаторов тегов. По умолчанию пустой массив. | `["coffee", "wifi"]`          |


#### Пример запроса

```http
POST /api/placemarkers HTTP/1.1
Content-Type: application/json
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

```json
{
  "name": "Любимая кофейня",
  "lat": 55.755826,
  "lon": 37.617299,
  "description": "Отличный кофе и круассаны",
  "type_id": "cafe",
  "tags": ["coffee", "wifi"]
}
```

### Responses (Ответы)

#### 🟢 201 Created — Успешное создание

Метка успешно создана и сохранена в базе данных.

```json
{
  "id": "123e4567-e89b-12d3-a456-426614174000",
  "name": "Любимая кофейня",
  "lat": "55.75582600",
  "lon": "37.61729900",
  "type_id": "cafe",
  "tags": [
    "coffee",
    "wifi"
  ],
  "description": "Отличный кофе и круассаны",
  "status": true,
  "msg": "Метка сохранена"
}
```

#### 🔴 400 Bad Request — Некорректный запрос

Возвращается при синтаксических ошибках в JSON.

```json
{
  "errors": [
    {
      "message": "Invalid JSON."
    }
  ]
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

Возвращается, если пропущены обязательные параметры (`name`, `lat`, `lon`) или их значения не прошли валидацию (например, `lat`/`lon` не являются числами, координаты вне допустимого диапазона, теги не являются массивом строк).

```json
{
  "errors": [
    {
      "message": "Missing required parameter \"name\"."
    }
  ]
}
```

```json
{
  "errors": [
    {
      "message": "Coordinates must be numeric."
    }
  ]
}
```

---

## Обновление метки (Update Placemarker)

Обновляет название, описание, тип и теги существующей метки. Координаты (`lat`, `lon`) через этот эндпоинт изменить нельзя.

**URL:** `/api/placemarkers/{id}`  
**Метод:** `PUT`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)

- `Content-Type: application/json`
- `Authorization: Bearer <token>`

#### Параметры пути (Path Parameters)

| Параметр | Тип    | Обязательное | Описание             | Пример                                   |
| -------- | ------ | ------------ | -------------------- | ---------------------------------------- |
| `id`     | `uuid` | **Да**       | Идентификатор метки. | `"123e4567-e89b-12d3-a456-426614174000"` |




#### Тело запроса (JSON Body)


| Поле          | Тип             | Обязательное | Описание                                                     | Пример                  |
| ------------- | --------------- | ------------ | ------------------------------------------------------------ | ----------------------- |
| `name`        | `string`        | **Да**       | Новое название метки.                                        | `"Обновлённая кофейня"` |
| `description` | `string`        | Нет          | Новое описание. По умолчанию пустая строка.                  | `"Теперь с десертами"`  |
| `type_id`     | `string`        | Нет          | Новый идентификатор типа. Если не передан — тип не меняется. | `"restaurant"`          |
| `tags`        | `array<string>` | Нет          | Новый список тегов. Если не передан — теги не меняются.      | `["coffee", "dessert"]` |


#### Пример запроса

```http
PUT /api/placemarkers/123e4567-e89b-12d3-a456-426614174000 HTTP/1.1
Content-Type: application/json
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

```json
{
  "name": "Обновлённая кофейня",
  "description": "Теперь с десертами",
  "type_id": "restaurant",
  "tags": ["coffee", "dessert"]
}
```

### Responses (Ответы)

#### 🟢 200 OK — Успешное обновление

Метка успешно обновлена.

```json
{
  "id": "123e4567-e89b-12d3-a456-426614174000",
  "name": "Обновлённая кофейня",
  "lat": "55.75582600",
  "lon": "37.61729900",
  "type_id": "restaurant",
  "tags": [
    "coffee",
    "dessert"
  ],
  "description": "Теперь с десертами",
  "status": true,
  "msg": "Метка обновлена"
}
```

#### 🔴 400 Bad Request — Некорректный запрос

Возвращается при синтаксических ошибках в JSON.

```json
{
  "errors": [
    {
      "message": "Invalid JSON."
    }
  ]
}
```
#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 404 Not Found — Метка не найдена

Возвращается, если метка с указанным `id` не существует.

```json
{
  "errors": [
    {
      "message": "Placemarker with id \"123e4567-e89b-12d3-a456-426614174000\" was not found."
    }
  ]
}
```

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

Возвращается, если `id` в пути не является UUID, пропущен обязательный параметр `name` или переданы невалидные значения (`type_id` не строка, `tags` не массив строк и т.д.).

```json
{
  "errors": [
    {
      "message": "Missing required parameter \"name\"."
    }
  ]
}
```

---

## Удаление метки (Delete Placemarker)

Удаляет метку по идентификатору.

**URL:** `/api/placemarkers/{id}`  
**Метод:** `DELETE`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)

- `Authorization: Bearer <token>`

#### Параметры пути (Path Parameters)


| Параметр | Тип    | Обязательное | Описание             | Пример                                   |
| -------- | ------ | ------------ | -------------------- | ---------------------------------------- |
| `id`     | `uuid` | **Да**       | Идентификатор метки. | `"123e4567-e89b-12d3-a456-426614174000"` |




#### Пример запроса

```http
DELETE /api/placemarkers/123e4567-e89b-12d3-a456-426614174000 HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Responses (Ответы)

#### 🟢 200 OK — Успешное удаление

Метка успешно удалена.

```json
{
  "status": true,
  "msg": "Метка удалена"
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 404 Not Found — Метка не найдена

Возвращается, если метка с указанным `id` не существует.

```json
{
  "errors": [
    {
      "message": "Placemarker with id \"123e4567-e89b-12d3-a456-426614174000\" was not found."
    }
  ]
}
```
#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

Возвращается, если `id` в пути не является UUID.

```json
{
  "errors": [
    {
      "message": "Value \"invalid-id\" is not a valid UUID."
    }
  ]
}
```

---

## Создание тега (Create Tag)

Создает новый тег для текущего авторизованного пользователя.

**URL:** `/api/tags`  
**Метод:** `POST`  
**Авторизация:** Требуется (Bearer Token)

### Request (Запрос)

#### Заголовки (Headers)

- `Content-Type: application/json`
- `Authorization: Bearer <token>`

#### Тело запроса (JSON Body)


| Поле          | Тип      | Обязательное | Описание                                   | Пример                   |
| ------------- | -------- | ------------ | ------------------------------------------ | ------------------------ |
| `name`        | `string` | **Да**       | Название тега.                             | `"coffee"`               |
| `description` | `string` | Нет          | Описание тега. По умолчанию пустая строка. | `"Места с хорошим кофе"` |


#### Пример запроса

```http
POST /api/tags HTTP/1.1
Content-Type: application/json
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

```json
{
  "name": "coffee",
  "description": "Места с хорошим кофе"
}
```

### Responses (Ответы)

#### 🟢 201 Created — Успешное создание

Тег успешно создан.

```json
{
  "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "name": "coffee",
  "description": "Места с хорошим кофе",
  "status": true,
  "msg": "Tag created successfully"
}
```

#### 🔴 400 Bad Request — Некорректный запрос

Возвращается при синтаксических ошибках в JSON.

```json
{
  "errors": [
    {
      "message": "Invalid JSON."
    }
  ]
}
```

#### 🔴 401 Unauthorized — Ошибка авторизации

Возвращается, если пользователь не авторизован (отсутствует или недействителен токен).

#### 🔴 422 Unprocessable Entity — Ошибка валидации данных

Возвращается, если пропущен обязательный параметр `name` или переданы невалидные значения.

```json
{
  "errors": [
    {
      "message": "Missing required parameter \"name\"."
    }
  ]
}
```

---


## Проверка состояния сервиса (Health Check)

Проверяет доступность сервиса. Используется для мониторинга и оркестрации (Docker, Kubernetes).

**URL:** `/health`  
**Метод:** `GET`  
**Авторизация:** Не требуется

### Request (Запрос)

#### Пример запроса

```http
GET /health HTTP/1.1
```

### Responses (Ответы)

#### 🟢 200 OK — Сервис доступен

```json
{
  "status": "ok"
}
```
