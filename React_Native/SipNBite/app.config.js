/**
 * app.config.js
 * Evaluated by Expo CLI on any `npx expo` invocation.
 * Automatically verifies backend and database connections.
 */
try {
  require('./scripts/ensure-services')();
} catch (e) {
  // Silent fallback
}

module.exports = ({ config }) => {
  return {
    ...config,
  };
};
