/**
 * Theme API utilities — shared across all Alpine stores.
 * Import helpers in each store: import { apiGet, apiPost } from './theme-api.js'
 */

// Safe accessor for WordPress-injected starterData object
export function getThemeData(key, fallback) {
  return (typeof starterData !== 'undefined' && starterData[key] !== undefined) ? starterData[key] : fallback;
}

// Nonce getter — used by all fetch calls
export function getNonce() {
  return getThemeData('nonce', '');
}

// API base URL (injected by PHP or fallback). Strip trailing slash.
export function getApiBase() {
  return getThemeData('apiBase', '/wp-json/starter/v1').replace(/\/$/, '');
}

// GET request with WP nonce
export function apiGet(path) {
  return fetch(getApiBase() + path, {
    headers: { 'X-WP-Nonce': getNonce() }
  });
}

// POST request with WP nonce + JSON body
export function apiPost(path, body) {
  return fetch(getApiBase() + path, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': getNonce() },
    body: JSON.stringify(body)
  });
}

// PUT request with WP nonce + JSON body
export function apiPut(path, body) {
  return fetch(getApiBase() + path, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': getNonce() },
    body: JSON.stringify(body),
    credentials: 'include',
  });
}

// Currency formatter — customize locale and currency for your project
export function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 2 }).format(amount);
}
