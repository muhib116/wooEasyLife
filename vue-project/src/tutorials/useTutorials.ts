import axios from "axios";
import { ref, computed, onMounted } from "vue";

export const toggleVideoPlayer = ref(false);
export const activeTutorialList = ref([]);

export const tutorialsList = {
    dashboard: [
      {
        title: "",
        path: "https://www.youtube.com/watch?v=uFcrJJiDksY",
      },
      {
        title: "",
        path: "https://www.youtube.com/watch?v=2vd2eJb1JG0",
      },
      {
        title: "",
        path: "https://www.youtube.com/watch?v=8I9jbS4_GxE",
      }
    ],
    orders: [
        {
          title: "",
          path: "https://www.youtube.com/watch?v=8I9jbS4_GxE",
        }
    ],
    missingOrders: [],
    blackList: [],
    fraudCheck: [],
    license: [],
    smsConfig: [],
    sendSms: [],
    integration: [],
    courier: [],
    customStatus: [],
    smsRecharge: [],
}

export const setActiveTutorialList = (category) => {
    if (tutorialsList[category] && tutorialsList[category].length) {
      activeTutorialList.value = tutorialsList[category];
      toggleVideoPlayer.value = true;
    } else {
      activeTutorialList.value = [];
      toggleVideoPlayer.value = false;
    }
}

export const useTutorials = () => {

  const extractVideoId = (url) => {
    const regex = /(?:youtube\.com\/.*v=|youtu\.be\/)([^?&]+)/;
    const match = url.match(regex);
    return match ? match[1] : null;
  };

  const currentIndex = ref(0);
  // Reactive states
  const hasActiveTutorials = computed(
    () => activeTutorialList.value.length > 0
  );

  const resetTutorials = () => {
    activeTutorialList.value = [];
    toggleVideoPlayer.value = false;
  };

  const currentVideoId = computed(() =>
    extractVideoId(activeTutorialList.value[currentIndex.value].path)
  );

  // Fetch video title using YouTube oEmbed API
  const fetchVideoTitle = async (videoObj: {
    path: string
    title: string
  }) => {
    try {
      const videoId = extractVideoId(videoObj.path)
      if (!videoId) return "Unknown Video"
      const { data } = await axios.get(
        `https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=${videoId}&format=json`
      )
      videoObj.title = data.title
    } catch (error) {
      console.error("Error fetching video title:", error)
      return "Unknown Video"
    }
  }

  return {
    currentIndex,
    tutorialsList,
    currentVideoId,
    toggleVideoPlayer,
    hasActiveTutorials,
    activeTutorialList,
    extractVideoId,
    resetTutorials,
    setActiveTutorialList,
    fetchVideoTitle
  };
};
