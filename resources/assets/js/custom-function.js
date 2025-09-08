$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  xhrFields: {
    withCredentials: true
  }
});

window.showErrorAlert = function showErrorAlert(message) {
  if (window.Swal !== 'undefined') {
    Swal.fire({
      title: 'Error!',
      text: message || 'Something went wrong. Please try again.',
      icon: 'error',
      customClass: {
        confirmButton: 'btn btn-primary'
      },
      buttonsStyling: false
    });
  } else {
    console.error('Sweet Alert is not initialized yet!');
  }
};

window.showDeleteAlert = function showDeleteAlert(message) {
  if (window.Swal !== 'undefined') {
    Swal.fire({
      icon: 'success',
      title: 'Deleted!',
      text: message,
      timer: 1000,
      showConfirmButton: false
    });
  } else {
    console.error('Sweet Alert is not initialized yet!');
  }
};

window.showRestoreAlert = function showRestoreAlert(message) {
  if (window.Swal !== 'undefined') {
    Swal.fire({
      icon: 'info',
      title: 'Restored!',
      text: message,
      timer: 1000,
      showConfirmButton: false
    });
  } else {
    console.error('Sweet Alert is not initialized yet!');
  }
};

window.showSuccessAlert = function (message, title = 'Saved!') {
  if (window.Swal !== 'undefined') {
    Swal.fire({
      icon: 'success',
      title: title,
      text: message,
      timer: 1000,
      showConfirmButton: false
    });
  } else {
    console.error('Sweet Alert is not initialized yet!');
  }
};

window.showInfoAlert = function (message) {
  if (window.Swal !== 'undefined') {
    Swal.fire({
      icon: 'info',
      title: 'Info',
      text: message,
      timer: 1500,
      showConfirmButton: false
    });
  } else {
    console.error('Sweet Alert is not initialized yet!');
  }
};

window.showInfoAlertHtml = function (message) {
  if (window.Swal !== 'undefined') {
    Swal.fire({
      icon: 'info',
      title: 'Info',
      html: message,
      customClass: {
        confirmButton: 'btn btn-info'
      },
      buttonsStyling: false
    });
  } else {
    console.error('Sweet Alert is not initialized yet!');
  }
};

window.showConfirmDeleteAlert = function (message, onDelete) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will delete ' + message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete!',
    customClass: {
      confirmButton: 'btn btn-danger me-3',
      cancelButton: 'btn btn-label-secondary'
    },
    buttonsStyling: false
  }).then(function (result) {
    if (result.value) {
      if (typeof onDelete === 'function') {
        onDelete();
      }
    }
  });
};

window.resetFormValidation = function resetFormValidation($form) {
  $form[0].reset();
  if (typeof FormValidation !== 'undefined') {
    const fvInstance = $form[0].formValidationInstance;
    fvInstance.resetForm(true);
  }
};

window.isNotNullOrEmpty = function (str) {
  return str !== null && str !== 'undefined' && str !== '';
};

window.isArrayNotNullOrEmpty = function (arr) {
  return arr?.length > 0;
};

window.convertToDouble = function (data) {
  return data != null && !isNaN(data) ? numeral(data).format('0,0.000') : '';
};

window.convertToDoubleOrElse = function (data, str) {
  return data != null && !isNaN(data) ? numeral(data).format('0,0.000') : str;
};

window.convertToThousand = function (data) {
  return data != null && !isNaN(data) ? numeral(data).format('0,0') : '';
};

window.convertToThousandOrElse = function (data, str) {
  return data != null && !isNaN(data) ? numeral(data).format('0,0') : str;
};

window.showHideBlockUI = function (isShow, $el) {
  if (isShow) {
    $el.block({
      message: '<div class="spinner-border text-primary" role="status"></div>',
      css: {
        backgroundColor: 'transparent',
        border: '0'
      },
      overlayCSS: {
        backgroundColor: '#fff',
        opacity: 0.8
      }
    });
  } else {
    $el.unblock();
  }
};
