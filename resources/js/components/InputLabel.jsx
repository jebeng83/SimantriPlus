import React from 'react';

export default function InputLabel({ htmlFor, value, className = '' }) {
  return (
    <label htmlFor={htmlFor} className={`block text-sm font-medium text-gray-700 ${className}`}>
      {value}
    </label>
  );
}