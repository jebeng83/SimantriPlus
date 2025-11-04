// Unified toast helper with a "cool" look by using SweetAlert2 (if available),
// falling back to Toastify, and then to console/alert.
// Supports both APIs:
//  - toast('Message', 'success' | 'error' | 'info' | 'warning', options)
//  - toast.success('Message', options), toast.error(...), toast.info(...), toast.warning(...)

function pickColor(type) {
  switch (String(type)) {
    case 'success':
      return '#10b981'; // emerald-500
    case 'error':
      return '#ef4444'; // red-500
    case 'warning':
      return '#f59e0b'; // amber-500
    case 'info':
    default:
      return '#3b82f6'; // blue-500
  }
}

function showWithSweetAlert2(message, type, options) {
  try {
    const Swal = window && window.Swal ? window.Swal : null;
    if (!Swal) return false;
    const color = pickColor(type);
    const html = options?.html ?? undefined;
    Swal.fire({
      toast: true,
      position: options?.position || 'top-end',
      icon: type || 'info',
      title: typeof message === 'string' ? message : '',
      html,
      showConfirmButton: false,
      timer: options?.duration ?? 2400,
      timerProgressBar: true,
      color: options?.textColor || undefined,
      background: options?.background || undefined,
      customClass: {
        popup: 'shadow-lg rounded-lg',
        title: 'text-sm',
      },
      didOpen: (toastEl) => {
        // Accent bar on the left for extra flair
        try {
          toastEl.style.borderLeft = `4px solid ${color}`;
        } catch {}
      },
    });
    return true;
  } catch {
    return false;
  }
}

function showWithToastify(message, type, options) {
  try {
    const Toastify = window && window.Toastify ? window.Toastify : null;
    if (!Toastify) return false;
    const bg = options?.background || `linear-gradient(90deg, ${pickColor(type)} 0%, #111827 100%)`;
    const gravity = options?.gravity || 'top';
    const position = options?.position || 'right';
    Toastify({
      text: typeof message === 'string' ? message : String(message),
      duration: options?.duration ?? 3000,
      close: true,
      gravity,
      position,
      stopOnFocus: true,
      className: type ? `toast-${type}` : undefined,
      style: { background: bg },
    }).showToast();
    return true;
  } catch {
    return false;
  }
}

export function toast(message, type = 'info', options = {}) {
  // Try modern toast engines in order
  if (showWithSweetAlert2(message, type, options)) return;
  if (showWithToastify(message, type, options)) return;
  // Fallbacks
  try {
    const prefix = {
      success: '[SUCCESS]',
      error: '[ERROR]',
      warning: '[WARNING]',
      info: '[INFO]',
    }[type] || '[INFO]';
    if (type === 'error') {
      console.error(prefix, message);
      alert(typeof message === 'string' ? message : String(message));
    } else {
      console.log(prefix, message);
    }
  } catch {}
}

toast.success = (msg, options = {}) => toast(msg, 'success', options);
toast.error = (msg, options = {}) => toast(msg, 'error', options);
toast.info = (msg, options = {}) => toast(msg, 'info', options);
toast.warning = (msg, options = {}) => toast(msg, 'warning', options);

export default toast;