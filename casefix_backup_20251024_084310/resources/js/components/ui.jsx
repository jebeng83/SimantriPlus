import React from 'react';

// Lightweight UI primitives based on Bootstrap classes
// This file provides minimal components so pages that import '@/Components/ui'
// can render without bringing in a heavy UI library.

export function Card({ children, className = '', ...props }) {
  return (
    <div className={`card ${className}`} {...props}>
      {children}
    </div>
  );
}

export function CardHeader({ children, className = '', ...props }) {
  return (
    <div className={`card-header ${className}`} {...props}>
      {children}
    </div>
  );
}

export function CardTitle({ children, className = '', ...props }) {
  return (
    <h5 className={`card-title ${className}`} {...props}>
      {children}
    </h5>
  );
}

export function CardContent({ children, className = '', ...props }) {
  return (
    <div className={`card-body ${className}`} {...props}>
      {children}
    </div>
  );
}

export function Button({ children, className = '', variant = 'primary', size, ...props }) {
  const sizeClass = size === 'sm' ? 'btn-sm' : size === 'lg' ? 'btn-lg' : '';
  const variantClass = variant ? `btn-${variant}` : 'btn-primary';
  return (
    <button className={`btn ${variantClass} ${sizeClass} ${className}`} {...props}>
      {children}
    </button>
  );
}

export function Input({ className = '', ...props }) {
  return <input className={`form-control ${className}`} {...props} />;
}

export function Label({ children, className = '', ...props }) {
  return (
    <label className={`form-label ${className}`} {...props}>
      {children}
    </label>
  );
}

export function Textarea({ className = '', ...props }) {
  return <textarea className={`form-control ${className}`} {...props} />;
}

export function Table({ children, className = '', striped = true, hover = true, ...props }) {
  const cls = `table ${striped ? 'table-striped' : ''} ${hover ? 'table-hover' : ''} ${className}`;
  return (
    <table className={cls} {...props}>
      {children}
    </table>
  );
}

export function TableHeader({ children, className = '', ...props }) {
  return (
    <thead className={className} {...props}>
      {children}
    </thead>
  );
}

export function TableBody({ children, className = '', ...props }) {
  return (
    <tbody className={className} {...props}>
      {children}
    </tbody>
  );
}

export function TableRow({ children, className = '', ...props }) {
  return (
    <tr className={className} {...props}>
      {children}
    </tr>
  );
}

export function TableHead({ children, className = '', ...props }) {
  return (
    <th className={className} {...props}>
      {children}
    </th>
  );
}

export function TableCell({ children, className = '', ...props }) {
  return (
    <td className={className} {...props}>
      {children}
    </td>
  );
}

export function Badge({ children, className = '', variant = 'secondary', ...props }) {
  const variantClass = variant ? `bg-${variant}` : 'bg-secondary';
  return (
    <span className={`badge ${variantClass} ${className}`} {...props}>
      {children}
    </span>
  );
}