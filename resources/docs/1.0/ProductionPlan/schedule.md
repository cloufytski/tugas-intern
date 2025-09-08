# Production - Schedule

---

- [Schedule Table](#schedule-table)
- [Generate DPS](#schedule-generate)
- [Schedule Daily](#schedule-day-modal)
- [Update Schedule](#schedule-update)
- [Upload Schedule](#schedule-upload-modal)
- [Modify Schedule](#schedule-register)

Pages are available to Role **production-planner**

By default, Schedule table shows this month schedule.

| Page                                                                                                                                                   | Description                                                                                                                                                                                                                                        |
| ------------------------------------------------------------------------------------------------------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| <a name="schedule-table"></a>`/production/schedules`<br />![schedule](/images/docs/ProductionPlan/schedule.png 'Schedule')                             | User view Schedule to run Mode filtered by Plant.<br />User can change date range to show certain schedules.<br />User can show/hide section based on filter.                                                                                      |
| <a name="schedule-generate"></a>![schedule-generate-dps](/images/docs/ProductionPlan/schedule-generate-dps.png 'Schedule Generate')                    | User refresh Prodsum calculation in DPS projection based on schedule table. <br/><br/>This button will take some time, so pelase do not click this button repeatedly, or close the tab.                                                            |
| <a name="schedule-day-modal"></a>![schedule-day](/images/docs/ProductionPlan/schedule-day.png 'Schedule Day')                                          | User click on _Week Start_, it shows the schedule within a day and the used up materials.                                                                                                                                                          |
| <a name="schedule-update"></a>![schedule-update](/images/docs/ProductionPlan/schedule-update.png 'Schedule Update')                                    | User click on _Schedule Mode_, it opens modal to update Schedule by changing Mode or capacity. User need to type at least 2 characters to show list of available modes.                                                                            |
| ![schedule-create](/images/docs/ProductionPlan/schedule-create.png 'Schedule Create')                                                                  | User click on empty *Schedule Mode*, it opens modal to enter new Schedule. User should specify Section first.                                                                                                                                      |
| <a name="schedule-upload-modal"></a>![schedule-upload](/images/docs/ProductionPlan/schedule-upload.png 'Schedule Upload')                              | User upload schedule from Excel file following specified headers.<br /><br />Week Start > [Section name] > Capacity > [Section name] > Capacity > ...                                                                                              |
| `/production/schedule/create`<br />![schedule-modify](/images/docs/ProductionPlan/schedule-modify.png 'Schedule Modify')                               | User modify existing schedule by entering date range, mode, and capacity.                                                                                                                                                                          |
| <a name="schedule-register"></a>![schedule-modify-simulation](/images/docs/ProductionPlan/schedule-modify-simulation.png 'Schedule Modify Simulation') | User can see simulation of changed schedules and the fluctuation of affected materials per Plant.<br /><br />If user satisfies with the changed mode, user click on Save button. If user click on Cancel button, the simulation will not be saved. |
