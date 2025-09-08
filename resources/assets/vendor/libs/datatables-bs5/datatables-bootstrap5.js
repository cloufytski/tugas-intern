import 'pdfmake/build/vfs_fonts';
import 'datatables.net-bs5';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-select-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';
import 'datatables.net-rowgroup-bs5';
import Checkbox from 'jquery-datatables-checkboxes';

$.fn.dataTable.ext.Checkbox = Checkbox(window, $);
