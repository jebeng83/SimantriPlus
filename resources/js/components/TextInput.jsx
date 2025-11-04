import React from 'react';

export default function TextInput({ className = '', ...props }) {
  return (
    <input
      {...props}
      className={`rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 border-gray-300 focus:ring-indigo-300 ${className}`}
    />
  );
}