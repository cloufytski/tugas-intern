import autosize from 'autosize';

try {
  window.autosize = autosize;
} catch (e) {}

export { autosize };

const textarea = $('textarea');
if (textarea) {
  autosize(textarea);
}
