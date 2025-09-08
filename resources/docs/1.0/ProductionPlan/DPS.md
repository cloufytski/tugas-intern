# Production - DPS

---

- [Prodsum Table](#prodsum-table)
- [Prodsum Projection](#prodsum-projection-table)
- [Prodsum Actual](#prodsum-actual-table)
- [Prodsum Plan](#prodsum-plan-table)
- [Prodsum Plan Version](#plan-version-modal)

Pages are available to Role **production-planner**

By default, DPS table shows this month schedule.

| Page                                                                                                                                  | Description                                                                                                                                                                                                        |
| ------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| <a name="prodsum-table"></a>![prodsum](/images/docs/ProductionPlan/prodsum.png 'Prodsum')                                             | User view prodsum table filtered by Plant. User can hover on the value to see both Projection and Actual value.<br /><br />The green-colored dates show past day, and the purple-colored dates show upcoming days. |
| <a name="prodsum-projection-table"></a>![prodsum-projection](/images/docs/ProductionPlan/prodsum-projection.png 'Prodsum Projection') | User view prodsum projection table filtered by Plant. <br/><br/>User can refresh DPS calculation based on Schedule by clicking on "Generate" button.                                                               |
| <a name="prodsum-actual-table"></a>![prodsum-actual](/images/docs/ProductionPlan/prodsum-actual.png 'Prodsum Actual')                 | User view prodsum actual table filtered by Plant.                                                                                                                                                                  |
| ![prodsum-actual-excel](/images/docs/ProductionPlan/prodsum-actual-excel.png 'Prodsum Actual Excel')                                  | User fill Actual Nett production **manually**. By default, selected date is today. <br /><br /> _Future Development_ is to auto-calculate from Daily Log Sheet (est. early of 2026).                               |
| ![prodsum-actual-day](/images/docs/ProductionPlan/prodsum-actual-day.png 'Prodsum Actual Day')                                        | User click on table cell and edit per day - material.                                                                                                                                                              |
| <a name="prodsum-plan-table"></a>![prodsum-plan](/images/docs/ProductionPlan/prodsum-plan.png 'Prodsum Plan')                         | User view prodsum plan grouped by Section. <br/><br />User can download as Excel for Monthly Production Plan.                                                                                                      |
| <a name="plan-version-modal"></a>![prodsum-plan-version](/images/docs/ProductionPlan/prodsum-plan-version.png 'Prodsum Plan Version') | User view versions of DPS Planning. User can click button "Generate" to generate new version. User can click on Version link to view past version.                                                                 |
