import React, { useRef } from 'react';
import { Animated, StyleSheet, View } from 'react-native';
import {
  PanGestureHandler,
  PinchGestureHandler,
  State,
  type PanGestureHandlerStateChangeEvent,
  type PinchGestureHandlerStateChangeEvent,
} from 'react-native-gesture-handler';

const MIN_SCALE = 1;
const MAX_SCALE = 4;

type Props = {
  uri: string;
};

export default function PinchZoomImage({ uri }: Props) {
  const baseScale = useRef(new Animated.Value(1)).current;
  const pinchScale = useRef(new Animated.Value(1)).current;
  const translateX = useRef(new Animated.Value(0)).current;
  const translateY = useRef(new Animated.Value(0)).current;

  const scaleRef = useRef(1);
  const offsetRef = useRef({ x: 0, y: 0 });

  const pinchRef = useRef<PinchGestureHandler>(null);
  const panRef = useRef<PanGestureHandler>(null);

  const onPinchEvent = Animated.event([{ nativeEvent: { scale: pinchScale } }], {
    useNativeDriver: true,
  });

  const onPinchStateChange = ({ nativeEvent }: PinchGestureHandlerStateChangeEvent) => {
    if (nativeEvent.oldState !== State.ACTIVE) return;

    const next = Math.min(MAX_SCALE, Math.max(MIN_SCALE, scaleRef.current * nativeEvent.scale));
    scaleRef.current = next;
    baseScale.setValue(next);
    pinchScale.setValue(1);

    if (next <= MIN_SCALE) {
      offsetRef.current = { x: 0, y: 0 };
      translateX.setOffset(0);
      translateY.setOffset(0);
      translateX.setValue(0);
      translateY.setValue(0);
    }
  };

  const onPanEvent = Animated.event(
    [{ nativeEvent: { translationX: translateX, translationY: translateY } }],
    { useNativeDriver: true },
  );

  const onPanStateChange = ({ nativeEvent }: PanGestureHandlerStateChangeEvent) => {
    if (nativeEvent.oldState !== State.ACTIVE || scaleRef.current <= MIN_SCALE) return;

    offsetRef.current = {
      x: offsetRef.current.x + nativeEvent.translationX,
      y: offsetRef.current.y + nativeEvent.translationY,
    };
    translateX.setOffset(offsetRef.current.x);
    translateY.setOffset(offsetRef.current.y);
    translateX.setValue(0);
    translateY.setValue(0);
  };

  const animatedScale = Animated.multiply(baseScale, pinchScale);

  return (
    <View style={styles.container}>
      <PanGestureHandler
        ref={panRef}
        simultaneousHandlers={pinchRef}
        onGestureEvent={onPanEvent}
        onHandlerStateChange={onPanStateChange}
        minPointers={1}
        maxPointers={2}
        avgTouches>
        <Animated.View style={styles.container}>
          <PinchGestureHandler
            ref={pinchRef}
            simultaneousHandlers={panRef}
            onGestureEvent={onPinchEvent}
            onHandlerStateChange={onPinchStateChange}>
            <Animated.View style={styles.container}>
              <Animated.Image
                source={{ uri }}
                style={[
                  styles.image,
                  {
                    transform: [
                      { scale: animatedScale },
                      { translateX },
                      { translateY },
                    ],
                  },
                ]}
                resizeMode="contain"
              />
            </Animated.View>
          </PinchGestureHandler>
        </Animated.View>
      </PanGestureHandler>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    overflow: 'hidden',
  },
  image: {
    width: '100%',
    height: '100%',
  },
});
