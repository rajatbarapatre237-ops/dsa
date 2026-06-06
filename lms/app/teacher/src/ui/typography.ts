import { Platform, TextStyle } from 'react-native';

/** Android maps heavy iOS weights to native Roboto variants more reliably. */
export function platformWeight(weight: TextStyle['fontWeight'] = '400'): TextStyle {
  if (Platform.OS !== 'android') {
    return { fontWeight: weight };
  }

  if (weight === '800' || weight === '700' || weight === 'bold') {
    return { fontFamily: 'sans-serif-medium', fontWeight: 'bold' };
  }
  if (weight === '600' || weight === '500') {
    return { fontFamily: 'sans-serif-medium', fontWeight: 'normal' };
  }

  return { fontFamily: 'sans-serif', fontWeight: 'normal' };
}

export const androidInputStyle: TextStyle =
  Platform.OS === 'android'
    ? {
        fontFamily: 'sans-serif',
        includeFontPadding: false,
        textAlignVertical: 'center',
      }
    : {};

export const androidTabLabelStyle: TextStyle =
  Platform.OS === 'android'
    ? {
        fontFamily: 'sans-serif-medium',
        fontSize: 11,
        marginBottom: 2,
      }
    : { fontSize: 11 };
