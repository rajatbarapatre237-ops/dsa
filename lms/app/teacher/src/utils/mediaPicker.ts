import { Alert, PermissionsAndroid, Platform } from 'react-native';
import {
  launchCamera,
  launchImageLibrary,
  type Asset,
  type ImagePickerResponse,
} from 'react-native-image-picker';

export type PickedFile = { uri: string; name: string; type: string; key: string };

function assetToFile(asset: Asset, fallbackPrefix: string): PickedFile | null {
  if (!asset.uri) return null;
  const name = asset.fileName ?? `${fallbackPrefix}-${Date.now()}.jpg`;
  return {
    uri: asset.uri,
    name,
    type: asset.type ?? 'image/jpeg',
    key: `${asset.uri}-${name}-${Date.now()}-${Math.random()}`,
  };
}

function pickerError(response: ImagePickerResponse, label: string): boolean {
  if (response.didCancel) return true;
  if (response.errorCode) {
    Alert.alert(label, response.errorMessage ?? 'Could not open picker');
    return true;
  }
  return false;
}

async function requestCameraPermission(): Promise<boolean> {
  if (Platform.OS !== 'android') return true;

  const hasPermission = await PermissionsAndroid.check(PermissionsAndroid.PERMISSIONS.CAMERA);
  if (hasPermission) return true;

  const result = await PermissionsAndroid.request(PermissionsAndroid.PERMISSIONS.CAMERA, {
    title: 'Camera permission',
    message: 'Allow camera access to take photos for notes and assignments.',
    buttonPositive: 'Allow',
    buttonNegative: 'Cancel',
  });

  return result === PermissionsAndroid.RESULTS.GRANTED;
}

async function requestGalleryPermission(): Promise<boolean> {
  if (Platform.OS !== 'android') return true;

  if (Platform.Version >= 33) {
    const permission = PermissionsAndroid.PERMISSIONS.READ_MEDIA_IMAGES;
    const hasPermission = await PermissionsAndroid.check(permission);
    if (hasPermission) return true;
    const result = await PermissionsAndroid.request(permission, {
      title: 'Photos permission',
      message: 'Allow access to photos to attach images.',
      buttonPositive: 'Allow',
      buttonNegative: 'Cancel',
    });
    return result === PermissionsAndroid.RESULTS.GRANTED;
  }

  const permission = PermissionsAndroid.PERMISSIONS.READ_EXTERNAL_STORAGE;
  const hasPermission = await PermissionsAndroid.check(permission);
  if (hasPermission) return true;
  const result = await PermissionsAndroid.request(permission, {
    title: 'Storage permission',
    message: 'Allow access to photos to attach images.',
    buttonPositive: 'Allow',
    buttonNegative: 'Cancel',
  });
  return result === PermissionsAndroid.RESULTS.GRANTED;
}

export async function takePhoto(): Promise<PickedFile[]> {
  const allowed = await requestCameraPermission();
  if (!allowed) {
    Alert.alert('Permission needed', 'Camera access is required to take photos.');
    return [];
  }

  const result = await launchCamera({
    mediaType: 'photo',
    quality: 0.85,
    saveToPhotos: false,
    cameraType: 'back',
  });

  if (pickerError(result, 'Camera')) return [];

  return (result.assets ?? [])
    .map(asset => assetToFile(asset, 'photo'))
    .filter((file): file is PickedFile => file !== null);
}

export async function pickImagesFromGallery(): Promise<PickedFile[]> {
  const allowed = await requestGalleryPermission();
  if (!allowed) {
    Alert.alert('Permission needed', 'Photo library access is required to pick images.');
    return [];
  }

  const result = await launchImageLibrary({
    mediaType: 'photo',
    quality: 0.85,
    selectionLimit: 0,
  });

  if (pickerError(result, 'Gallery')) return [];

  return (result.assets ?? [])
    .map(asset => assetToFile(asset, 'image'))
    .filter((file): file is PickedFile => file !== null);
}
