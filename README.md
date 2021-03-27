<p align="center">
  <img
    width="250"
    src="adrgen.png"
    alt="Starship – Cross-shell prompt"
  />
</p>

<p align="center">
  badges
</p>

<p align="center">
  <a href="#installation">Installation</a>
  |
  <a href="#usage">Usage</a>
  |
  <a href="#license">License</a>
</p>

<h1></h1>

**The simplest and best generator ADR classes and directories**

<a name="installation"></a>

## Installation

### Prerequisites

- PHP 7.3 or newer
- Composer

### Getting Started

1. Install global package

   #### Install Latest Version
   ```sh
   composer require global dwdev/adrgen
   ```

<a name="usage"></a>

## Commands

| Command                 | Description                                | Options                    | Arguments  |
| ------------------------| ------------------------------------------ | ---------------------------| -----------|
| php adrgen make:crud    | Generates the basic CRUD operations files  | --actions_dir              | model      |

### Details
1. **--actions-dir**: your directory that will be the root folder for ADR actions directories
2. **model**: the model's name to be created

Example:
```sh
php adrgen make:crud user --action_dir=/var/www/project
```

### The default template created

    /var/www/project
    ├── (D) User                            # Capitalized model argument
    │   ├── (D) Index                       # Directory actions
    |   |   ├── (F) IndexUserAction.php     # Action file
    |   |   └── (F) IndexUserResponder.php  # Responder file
    │   ├── (D) Create
    |   |   ├── (F) CreateUserAction.php
    |   |   └── (F) CreateUserResponder.php
    │   ├── (D) Store
    |   |   ├── (F) StoreUserAction.php
    |   |   └── (F) StoreUserResponder.php
    │   ├── (D) Edit
    |   |   ├── (F) EditUserAction.php
    |   |   └── (F) EditUserResponder.php
    │   ├── (D) Update
    |   |   ├── (F) UpdateUserAction.php
    |   |   └── (F) UpdateUserResponder.php
    │   ├── (D) Delete
    |   |   ├── (F) DeleteUserAction.php
    |   |   └── (F) DeleteUserResponder.php
    └── └── (F) UserResponder.php           # Default super responder file

<a name="license"></a>

## License

MIT

## TODO
### make:crud improvements
- [ ] Don't overwrite existent action dir files
- [ ] Create option --only=create,update,patch
- [ ] Add laravel preset implementation (opinionated CRUD)
- [ ] Add symfony preset implementation (opinionated CRUD)

### make:payload new command
- [ ] Create new command to generate the domain payload pattern class
- [ ] Add laravel preset implementation
- [ ] Add symfony preset implementation
