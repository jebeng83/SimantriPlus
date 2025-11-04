import React from 'react';

// Minimal Select implementation compatible with the usage pattern
// <Select value onValueChange>
//   <SelectTrigger><SelectValue placeholder="..."/></SelectTrigger>
//   <SelectContent>
//     <SelectItem value="..."></SelectItem>
//   </SelectContent>
// </Select>

export function Select({ value, onValueChange, children, className = '', ...props }) {
  let placeholder = undefined;
  const options = [];

  React.Children.forEach(children, (child) => {
    if (!child || !child.type) return;
    if (child.type && child.type._isSelectTrigger) {
      // Find SelectValue placeholder
      React.Children.forEach(child.props.children, (grand) => {
        if (grand && grand.type && grand.type._isSelectValue) {
          placeholder = grand.props.placeholder;
        }
      });
    }
    if (child.type && child.type._isSelectContent) {
      React.Children.forEach(child.props.children, (item) => {
        if (item && item.type && item.type._isSelectItem) {
          options.push({ value: item.props.value ?? '', label: item.props.children });
        }
      });
    }
  });

  const finalOptions = [...options];
  if (placeholder !== undefined) {
    // Insert placeholder as the first empty option if not already provided
    const hasEmpty = options.some((o) => o.value === '');
    if (!hasEmpty) finalOptions.unshift({ value: '', label: placeholder });
  }

  return (
    <select
      className={`form-select ${className}`}
      value={value ?? ''}
      onChange={(e) => onValueChange?.(e.target.value)}
      {...props}
    >
      {finalOptions.map((opt, idx) => (
        <option key={idx} value={opt.value}>
          {opt.label}
        </option>
      ))}
    </select>
  );
}

export function SelectTrigger({ children }) {
  return null;
}
SelectTrigger._isSelectTrigger = true;

export function SelectValue({ placeholder }) {
  return null;
}
SelectValue._isSelectValue = true;

export function SelectContent({ children }) {
  return <>{children}</>;
}
SelectContent._isSelectContent = true;

export function SelectItem({ value, children }) {
  return <>{children}</>;
}
SelectItem._isSelectItem = true;