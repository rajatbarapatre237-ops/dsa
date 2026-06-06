/**
 * Drop-in replacement for react-native's NativeEventEmitter.
 * Delegates entirely to DeviceEventEmitter (RCTDeviceEventEmitter) so iOS
 * startup does not crash when a native module is null / not yet ready.
 */
'use strict';

import RCTDeviceEventEmitter from 'react-native/Libraries/EventEmitter/RCTDeviceEventEmitter';

class DeviceEventEmitterBridge {
  constructor(_nativeModule) {
    // Native module intentionally unused — events flow through DeviceEventEmitter.
  }

  addListener(eventType, listener, context) {
    return RCTDeviceEventEmitter.addListener(eventType, listener, context);
  }

  emit(eventType, ...args) {
    RCTDeviceEventEmitter.emit(eventType, ...args);
  }

  removeAllListeners(eventType) {
    if (eventType != null) {
      RCTDeviceEventEmitter.removeAllListeners(eventType);
    }
  }

  listenerCount(eventType) {
    return RCTDeviceEventEmitter.listenerCount(eventType);
  }
}

export default DeviceEventEmitterBridge;
