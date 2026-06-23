# REST API «Список задач»

> **Основная задача проекта:** Демонстрация знаний, умений и компетенций тестируемого PHP-разработчика. Проект спроектирован для показа уровня владения современным стеком (PHP 8.3, Laravel 12), понимания принципов SOLID, слоистой архитектуры, а также стандартов контроля качества кода.

## **Резюме проекта**
Данный файл является единой точкой входа и содержит краткую вытяжку из всей проектной документации. Система представляет собой надежный и масштабируемый интерфейс для интеграции с клиентскими приложениями.

* **Бизнес-требования ([base_tz.md](docs/base_tz.md)):** Реализация CRUD-операций для задач, поддержка поиска по подстроке, динамической сортировки (`due_date`, `created_at`) и постраничной навигации.
* **Архитектура ([technical_documentation.md](docs/technical_documentation.md)):** Изоляция бизнес-логики от HTTP-слоя. Использование паттернов проектирования (Strategy, Repository), передача данных через строго типизированные DTO и отсутствие прямой работы с БД в контроллерах.
* **Инфраструктура ([SETUP.md](docs/SETUP.md)):** Полная контейнеризация среды выполнения на базе Docker (Nginx, PHP-FPM, MySQL, Redis). Подготовка и запуск приложения одной командой.
* **Контроль качества ([TESTING.md](docs/TESTING.md)):** Автотесты (50 сценариев), PHPStan level 9, Laravel Pint, CI в GitHub Actions.
* **Спецификация API ([openapi.yaml](docs/openapi.yaml)):** Исчерпывающее описание маршрутов, схем запросов и ответов по стандарту OpenAPI 3.0.

## **Swagger (бонус)**

В проекте подключён **Swagger UI** — интерактивная веб-страница для просмотра и ручной проверки API.

| | |
| --- | --- |
| **Адрес после запуска Docker** | http://localhost:8080/api/documentation |
| **Эталон контракта** | [docs/openapi.yaml](docs/openapi.yaml) |
| **Публикация** | [Swagger Hub](https://app.swaggerhub.com/apis/freelance-982-f00/test_case_php_developer/1.0.0) |

Swagger **не входит в обязательный рантайм** API: это удобство для проверяющего и разработчика — можно отправить запросы из браузера без Postman. Описание методов вынесено в слой `app/OpenApi/` и не смешано с контроллерами. Полнота схем и форматов ответов зафиксирована в `openapi.yaml`.

## **Документация**
Для детального погружения в проект используйте ссылки ниже:
- [docs/base_tz.md](docs/base_tz.md) | Исходное ТЗ заказчика |
- [docs/project_documentation.md](docs/project_documentation.md) | ВТЗ и НТЗ: требования, стек, архитектура, модель, API |
- [docs/technical_documentation.md](docs/technical_documentation.md) | Архитектурные решения и 17 направлений развития |
- [docs/SETUP.md](docs/SETUP.md) | Развёртывание в Docker, troubleshooting |
- [docs/TESTING.md](docs/TESTING.md) | Автотесты, Pint, PHPStan, CI, ручная верификация |
- [docs/CHECKLIST.md](docs/CHECKLIST.md) | Личный чек-лист разработки: этапы от аналитики до публикации |
- [docs/AUDIT.md](docs/AUDIT.md) | Аудит для менеджера: соответствие ТЗ, риски, дорожная карта |
- [docs/openapi.yaml](docs/openapi.yaml) | Контракт OpenAPI 3.0 |

## **Ссылки**
* **Профиль GitHub:** [github.com/nerolory](https://github.com/nerolory)
* **Репозиторий проекта:** [test_case_for_php_developer](https://github.com/nerolory/test_case_for_php_developer)
* **Резюме на hh.ru:** [резюме PHP-разработчика](https://hh.ru/resume/228aff0fff0ff3781a0039ed1f6c39736f644a)
* **Спецификация API (Swagger Hub):** [test_case_php_developer](https://app.swaggerhub.com/apis/freelance-982-f00/test_case_php_developer/1.0.0)
* **Форма сдачи тестового задания:** [24.future-group.ru](https://24.future-group.ru/pub/form/4/xnof82/)
| [docs/CHECKLIST.md](docs/CHECKLIST.md) | Личный чек-лист разработки: этапы от аналитики до публикации |
