'use strict';
// Configuration settings
const colors = {
  primary: '#7367f0',
  secondary: '#7983bb',
  success: '#28c76f',
  info: '#00cfe8',
  warning: '#ff9f43',
  danger: '#ea5455',
  dark: '#4b4b4b',
  black: '#000',
  white: '#fff',
  bodyBg: '#f8f7fa',
  bodyColor: '#6f6b7d',
  headingColor: '#5d596c',
  textMuted: '#a5a3ae',
  borderColor: '#dbdade'
};

// Labels colors
const colors_label = {
  primary: '#7367f029',
  secondary: '#a8aaae29',
  success: '#28c76f29',
  info: '#00cfe829',
  warning: '#ff9f4329',
  danger: '#ea545529',
  dark: '#4b4b4b29'
};

// Dark colors
const colors_dark = {
  bodyBg: '#2f3349',
  bodyColor: '#b6bee3',
  headingColor: '#cfd3ec',
  textMuted: '#7983bb',
  borderColor: '#434968'
};

// Config options
let config = {
  colors,
  colors_label,
  colors_dark,
  enableMenuLocalStorage: false
};

// Assets Path
let assetsPath = document.documentElement.getAttribute('data-assets-path'),
  templateName = document.documentElement.getAttribute('data-template'),
  rtlSupport = true;

// Template customizer
if (typeof TemplateCustomizer !== 'undefined') {
  window.templateCustomizer = new TemplateCustomizer({
    cssPath: assetsPath + 'vendor/css' + (rtlSupport ? '/rtl' : '') + '/',
    themesPath: assetsPath + 'vendor/css' + (rtlSupport ? '/rtl' : '') + '/',
    displayCustomizer: false,
    lang: 'en',
    defaultTheme: 1,
    defaultStyle: 'light',
    defaultTextDir: 'ltr',
    defaultLayoutType: 'fixed',
    defaultMenuCollapsed: false,
    defaultNavbarFixed: true,
    defaultFooterFixed: false,
    defaultShowDropdownOnHover: true
  });
}