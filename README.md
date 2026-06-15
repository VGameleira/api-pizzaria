# ApiJuca - REST API de Pizzaria

[![PHP](https://img.shields.io/badge/PHP-7.4-777BB4?logo=php)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7-4479A1?logo=mysql)](https://www.mysql.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

API REST para gerenciamento de pizzaria com CRUD completo de pizzas e bebidas. Desenvolvida em PHP puro com PDO e prepared statements.

### Endpoints

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/pizza/getAll.php` | Listar pizzas (paginado) |
| GET | `/api/pizza/getSingle.php?id=X` | Detalhes da pizza |
| POST | `/api/pizza/create.php` | Criar pizza |
| PUT | `/api/pizza/update.php` | Atualizar pizza |
| DELETE | `/api/pizza/delete.php` | Excluir pizza |
| GET | `/api/bebidas/getAll.php` | Listar bebidas |
| POST | `/api/bebidas/create.php` | Criar bebida |
| PUT | `/api/bebidas/update.php` | Atualizar bebida |
| DELETE | `/api/bebidas/delete.php` | Excluir bebida |

### Instalação

```bash
git clone https://github.com/VGameleira/ApiJuca.git
cd ApiJuca
mysql -u root -p < config/bd.sql
php -S localhost:8000
```

---

MIT License — Veja [LICENSE](LICENSE).

**Vinícius dos Santos Gameleira** — [@VGameleira](https://github.com/VGameleira)
