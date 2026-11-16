import cv2
import os

video_path = r"C:/Users/faiza/.gemini/antigravity/brain/7119c119-343f-4e0d-a171-eb32cb8a07bd/.user_uploaded/media_1794833325895_dcd0255b.mp4"
output_dir = r"C:/Users/faiza/.gemini/antigravity/brain/7119c119-343f-4e0d-a171-eb32cb8a07bd/frames_user"
os.makedirs(output_dir, exist_ok=True)

cap = cv2.VideoCapture(video_path)
fps = cap.get(cv2.CAP_PROP_FPS) or 30
total_frames = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))
duration = total_frames / fps
print(f"Video duration: {duration:.2f}s, FPS: {fps}, Total frames: {total_frames}")

interval = max(1, int(fps * 2)) # every 2 seconds
frame_idx = 0
saved_count = 0

while cap.isOpened():
    ret, frame = cap.read()
    if not ret:
        break
    if frame_idx % interval == 0:
        out_path = os.path.join(output_dir, f"frame_{saved_count:03d}.jpg")
        cv2.imwrite(out_path, frame)
        saved_count += 1
    frame_idx += 1

cap.release()
print(f"Saved {saved_count} frames to {output_dir}")
