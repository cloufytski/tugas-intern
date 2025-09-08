import jspreadsheet from 'jspreadsheet-ce';
import jsuites from 'jsuites';

try {
  window.jspreadsheet = jspreadsheet;
  window.jsuites = jsuites;
} catch (e) {}

export { jspreadsheet, jsuites };
