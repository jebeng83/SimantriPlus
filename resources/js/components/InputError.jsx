import React from 'react';

export default function InputError({ message, className = '' }) {
  if (!message) return null;
  return <p className={`text-xs text-red-600 ${className}`}>{message}</p>;
}