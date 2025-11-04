import React from 'react';

export default function Button({ children, className = '', variant = 'primary', size, ...props }) {
  const sizeClass = size === 'sm' ? 'btn-sm' : size === 'lg' ? 'btn-lg' : '';
  const variantClass = variant ? `btn-${variant}` : 'btn-primary';
  return (
    <button className={`btn ${variantClass} ${sizeClass} ${className}`} {...props}>
      {children}
    </button>
  );
}