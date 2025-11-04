import React from 'react';

// Minimal AppLayout wrapper to keep Farmasi pages working without Inertia layout.
// Extend or customize as needed (topbar, breadcrumbs, etc.).
export default function AppLayout({ children }) {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      {children}
    </div>
  );
}