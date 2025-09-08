# PSPA SCM

PSPA Supply Chain Management System for Sales and Production Planner PT Ecogreen Oleochemicals Batam

## Modules

in folder `Modules/` from [Laravel Modules](https://nwidart.com/laravel-modules/v6/introduction):

- ProductionPlan (division: PSPA)
- SalesPlan (division: PSPA)
- MaterialProc (division: PSPA / SMP **TBA**)
- InvBalance (division: PSPA)

## Database

Database postgresql with database name `pspa_scm`

### Migration

Seeders are both handled manually by bulk insert or read from Excel files (folder `csv_import/` in each module _database_ folders):

| Module         | Class                        | DB Table name                                            | Insert     | File                         | Description                                                                                                              |
| -------------- | ---------------------------- | -------------------------------------------------------- | ---------- | ---------------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| App            | PlantSeeder                  | plant_master                                             | Bulk (7)   |                              |                                                                                                                          |
|                | SectionSeeder                | section_master                                           | Bulk (33)  |                              | refer to DPS_Macro.xlsm Sheet 'Section'                                                                                  |
|                | MaterialClassSeeder          | material_class_master                                    | Bulk (6)   |                              | including MaterialClass, MaterialCategory, MaterialGroupSimple, MaterialGroup, MaterialPackagingClass, MaterialPackaging |
|                | MaterialCategorySeeder       | material_category_master                                 | Bulk (36)  |                              |                                                                                                                          |
|                | MaterialGroupSimpleSeeder    | material_group_simple_master                             | Bulk (296) |                              |                                                                                                                          |
|                | MaterialGroupSeeder          | material_group_master                                    | Bulk (370) |                              |                                                                                                                          |
|                | MaterialPackagingClassSeeder | material_packaging_class_master                          | Bulk (6)   |                              |                                                                                                                          |
|                | MaterialPackagingSeeder      | material_packaging_master                                | Bulk (10)  |                              |                                                                                                                          |
|                | MaterialSeeder               | material_master                                          | Excel      | _Material_Class_Master.xlsx_ | Sheet `Material`                                                                                                         |
| ProductionPlan | ModeSeeder                   | mode_ms                                                  | Excel      | _DPS_Macro.xlsm_             | Sheet `Mode`                                                                                                             |
| SalesPlan      | CountrySeeder                | country_ms                                               | Bulk       |                              |                                                                                                                          |
|                | CustomerSeeder               | customer_group_ms<br />customer_code_ms<br />customer_ms | Excel      | Customer_Master.xlsx         | CustomerGroupImport<br />CustomerCodeImport<br />CustomerNameImport                                                      |
|                | OrderStatusSeeder            | order_status_ms                                          | Bulk (6)   |                              |                                                                                                                          |
|                | OrderRescheduleCauseSeeder   | order_reschedule_cause_ms                                | Bulk (18)  |                              |                                                                                                                          |
|                | OrderProjectionSeeder        | order_projection_ts                                      | Excel      | Sales_Macro.xlsm             | Sheet 'OR Projection'                                                                                                    |
|                | OrderFinishedGoodsSeeder     | order_finished_goods_ts                                  | Excel      | Sales_Macro.xlsm             | Sheet 'OR Order FG'                                                                                                      |

Commands to run:

- Module
  - create new module: `php artisan module:make <module-name>`
- Migration
  - create migration file module App: `php artisan make:migration <migration-filename>`
  - create migration file Laravel Modules: `php artisan module:make-migration <migration-filename> <module-name>`
  - migrate module App: `php artisan migrate`
  - migrate Laravel Modules: `php artisan module:migrate <module-name>`
- Seeder
  - create seed file module App: `php artisan make:seed <migration-filename>`
  - create seed file Laravel Modules: `php artisan module:make-seed <migration-filename> <module-name>`
  - seed module App from DatabaseSeeder: `php artisan db:seed [--class=...]`
  - seed Laravel Modules from [ModuleName]DatabaseSeeder: `php artisan module:seed <module-name>`
- Routing
  - show route list: `php artisan route:list [--path=api]`
- Custom Command
  - generate prodsum projection `php artisan generate:prodsum [--status=projection (optional)] {startDate} {endDate} `
    This custom command will generate prodsum between date range and save it to table prodsum\_[status]. --status=actual **TBA**

## How to Run

1. `php artisan migrate` to run basic User migration
2. `php artisan serve` to check if Laravel is running

## External Libraries

#### Backend Laravel

- [maatwebsite/excel](https://docs.laravel-excel.com/3.1/imports/) = to read Excel files
- [nwidart/laravel-modules](https://nwidart.com/laravel-modules/v6/introduction) = modularization project
- laravel/sanctum = use Authorization Bearer to expose master data via API (plant_master, section_master)
- [scramble/dedoc](https://scramble.dedoc.co/) = API documentation in swagger with link _/docs/api_

#### Frontend Laravel Blade + vanilla JS

- [JSpreadsheet CE](https://bossanova.uk/jspreadsheet/docs/getting-started) v5 = to show data in Excel-like spreadsheet (free version)
- [WebDataRocks](https://www.webdatarocks.com/doc/js/integration-with-jquery/) = to show Excel-like pivot table
