import React from 'react';
import { motion } from 'framer-motion';

export default function HelloMotion() {
  return (
    <div style={{ padding: '12px' }}>
      <motion.div
        initial={{ opacity: 0, y: -10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        style={{
          background: '#0d6efd',
          color: '#fff',
          borderRadius: 8,
          padding: '10px 14px',
          display: 'inline-block',
          boxShadow: '0 4px 10px rgba(13,110,253,0.3)'
        }}
      >
        React + Motion aktif!
      </motion.div>
    </div>
  );
}