import React from 'react';

export default function Badge({ children, className = '', variant = 'secondary', ...props }) {
  const variantClass = variant ? `bg-${variant}` : 'bg-secondary';
  return (
    <span className={`badge ${variantClass} ${className}`} {...props}>
      {children}
    </span>
  );
}