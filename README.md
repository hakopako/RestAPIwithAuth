# Simple REST API + Auth


## Technology

- docker-compose
- PHP 7.1
- Postgres 5.6

### Installation

Execute `setup.sh` to set application up on your machine.  

```
$ git clone [repository]
$ cd [repository]
$ sh setup.sh
```

The script consists:

- Stop running containers
- Re-create application containers
- Run composer install
- Run tests
- Launch application containers

### Test

If you want to run tests after installation, try the command below:

```
$ docker exec -it php vendor/bin/phpunit
```


## Requirements

The Sample API conform to REST practices and provide the following functionality:

- List, create, read, update, and delete Cook-Recipes
- Search Cook-Recipes
- Rate Cook-Recipes

### Table Schema

- [users](https://github.com/hakopako/RestAPIwithAuth/tree/master/database/schema/2018_04_07_00_create_users_table.sql)
- [recipes](https://github.com/hakopako/RestAPIwithAuth/tree/master/database/schema/2018_04_06_00_create_recipes_table.sql)
- [ratings](https://github.com/hakopako/RestAPIwithAuth/tree/master/database/schema/2018_04_06_01_create_ratings_table.sql)

### Endpoints

Endpoints specified as protected below requires authentication to view.

##### Authentication

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| Login  | `POST`       | `/login`              | ✘         |

```
ex)
$ curl -X POST "http://recipe:recipe@localhost/login"

{
  "status":200,
  "data":{
    "token":"d9b183ff6e6e15819ed7f4230643a04f",
    "expire":"2018-04-11 09:55:35"
  }
}
```

##### Recipes

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| List   | `GET`       | `/recipes`             | ✘         |

```
ex)
$ curl -X GET "http://localhost/recipes"

{
  "status":200,
  "data":[ .... ]
}
```

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| Create | `POST`      | `/recipes`             | ✓         |


```
ex)
$ curl -X POST "http://localhost/recipes" \
       -H "X-RECIPE-TOKEN: d9b183ff6e6e15819ed7f4230643a04f"\
       -d '{"name": "test_recipe", "prep_time": 20, "is_vegetarian": "f", "difficulty": 2}'

{
 "status":200,
 "data":[]
}
```

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| Get    | `GET`       | `/recipes/{id}`        | ✘         |

```
ex)
$ curl -X GET "http://localhost/recipes/2"

{
  status: 200,
  data: {
      id: 2,
      name: "test_recipe",
      prep_time: 20,
      difficulty: 2,
      is_vegetarian: false,
      is_valid: true,
      created_at: "2018-04-10 10:05:31.077068",
      updated_at: "2018-04-10 10:05:31.077068"
    }
}
```

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| Update | `PUT`       | `/recipes/{id}`        | ✓         |

```
ex)
$ curl -X PUT "http://localhost/recipes/2" \
       -H "X-RECIPE-TOKEN: d9b183ff6e6e15819ed7f4230643a04f"\
       -d '{"name": "test_recipe_update"}'

{
 "status":200,
 "data":[]
}
```


| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| Delete | `DELETE`    | `/recipes/{id}`        | ✓         |

```
ex)
$ curl -X DELETE "http://localhost/recipes/2" \
       -H "X-RECIPE-TOKEN: d9b183ff6e6e15819ed7f4230643a04f"

{
 "status":200,
 "data":[]
}
```

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| Rate   | `POST`      | `/recipes/{id}/rating` | ✘         |

```
ex)
$ curl -X POST "http://localhost/recipes/2/rating" \
       -d '{"score": 5}'

{
 "status":200,
 "data":[]
}
```

| Name     | Method     | URL                           | Protected |
| ---      | ---        | ---                           | ---       |
| Search   | `GET`      | `/recipes/search?q={keyword}` | ✘         |

```
ex)
$ curl -X GET "http://localhost/recipes/search?q=something"

{
 "status":200,
 "data":[ ... ]
}
```
