import React from 'react';
import { Platform, Text, TextStyle, View, ViewStyle, StyleProp } from 'react-native';
import IoniconsGlyphs from '../assets/glyphmaps/Ionicons.json';
import MaterialGlyphs from '../assets/glyphmaps/MaterialCommunityIcons.json';
import { PRIMARY } from '../config';

type IconFamily = 'ionicons' | 'material';

type Props = {
  name: string;
  family?: IconFamily;
  size?: number;
  color?: string;
  style?: StyleProp<TextStyle | ViewStyle>;
};

function resolveFontFamily(family: IconFamily): string {
  if (family === 'ionicons') {
    return 'Ionicons';
  }
  return Platform.select({
    ios: 'Material Design Icons',
    android: 'MaterialCommunityIcons',
    default: 'MaterialCommunityIcons',
  })!;
}

const GLYPH_MAP: Record<IconFamily, Record<string, number>> = {
  ionicons: IoniconsGlyphs,
  material: MaterialGlyphs,
};

function glyphChar(family: IconFamily, name: string) {
  const code = GLYPH_MAP[family][name];
  return typeof code === 'number' ? String.fromCodePoint(code) : '?';
}

export default function AppIcon({
  name,
  family = 'ionicons',
  size = 22,
  color = PRIMARY,
  style,
}: Props) {
  const glyph = glyphChar(family, name);
  const fontFamily = resolveFontFamily(family);

  if (Platform.OS === 'android') {
    return (
      <View
        style={[
          {
            width: size,
            height: size,
            alignItems: 'center',
            justifyContent: 'center',
          },
          style as ViewStyle,
        ]}>
        <Text
          allowFontScaling={false}
          style={{
            fontFamily,
            fontSize: size,
            lineHeight: size,
            width: size,
            height: size,
            textAlign: 'center',
            textAlignVertical: 'center',
            color,
            includeFontPadding: false,
          }}>
          {glyph}
        </Text>
      </View>
    );
  }

  return (
    <Text
      allowFontScaling={false}
      style={[
        {
          fontFamily,
          fontSize: size,
          color,
          fontWeight: 'normal',
        },
        style as TextStyle,
      ]}>
      {glyph}
    </Text>
  );
}
