const path = require('path');
const { getDefaultConfig, mergeConfig } = require('@react-native/metro-config');

const NATIVE_EVENT_EMITTER_SHIM = path.resolve(
  __dirname,
  'shims/NativeEventEmitter.js',
);

/**
 * Metro configuration
 * https://reactnative.dev/docs/metro
 *
 * @type {import('@react-native/metro-config').MetroConfig}
 */
const config = {
  resolver: {
    resolveRequest: (context, realModuleName, platform, moduleName) => {
      const isNativeEventEmitter =
        realModuleName.includes(
          `${path.sep}Libraries${path.sep}EventEmitter${path.sep}NativeEventEmitter`,
        ) ||
        realModuleName.endsWith('Libraries/EventEmitter/NativeEventEmitter') ||
        realModuleName.endsWith('Libraries/EventEmitter/NativeEventEmitter.js');

      if (isNativeEventEmitter) {
        return {
          filePath: NATIVE_EVENT_EMITTER_SHIM,
          type: 'sourceFile',
        };
      }

      return context.resolveRequest(
        context,
        realModuleName,
        platform,
        moduleName,
      );
    },
  },
};

module.exports = mergeConfig(getDefaultConfig(__dirname), config);
