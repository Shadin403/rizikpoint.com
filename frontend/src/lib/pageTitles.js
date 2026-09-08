/**
 * Central registry of page titles (en + bn) and optional descriptions.
 * Pages import this and pass their entry to usePageTitle(...).
 */
export const pageTitles = {
  Home: {
    en: "Organic & Everyday Groceries",
    bn: "অর্গানিক ও প্রতিদিনের গ্রোসারি",
    description: {
      en: "Explore organic favourites and everyday grocery essentials for your kitchen, all in one place.",
      bn: "পছন্দের অর্গানিক পণ্য ও রান্নাঘরের প্রতিদিনের গ্রোসারি খুঁজুন এক জায়গায়।",
    },
  },
  ProductsList: {
    en: "All Products",
    bn: "সকল পণ্য",
    description: {
      en: "Browse all products available on DealPabo with the best prices in Bangladesh.",
      bn: "DealPabo তে বাংলাদেশের সেরা দামে সকল পণ্য দেখুন।",
    },
  },
  DealDetail: {
    en: "Deal Details",
    bn: "ডিল বিস্তারিত",
    description: {
      en: "View full details of this deal, including price, discount, store info, and how to grab it.",
      bn: "এই ডিলের সম্পূর্ণ বিস্তারিত দেখুন — মূল্য, ছাড়, স্টোর তথ্য এবং কীভাবে পাবেন।",
    },
  },
  Stores: {
    en: "All Stores",
    bn: "সকল স্টোর",
    description: {
      en: "Browse all stores featured on DealPabo and find exclusive offers from each one.",
      bn: "DealPabo তে থাকা সকল স্টোর দেখুন এবং প্রতিটি থেকে এক্সক্লুসিভ অফার খুঁজুন।",
    },
  },
  StoreDetail: {
    en: "Store Details",
    bn: "স্টোর বিস্তারিত",
    description: {
      en: "View all deals, coupons, and offers from this store.",
      bn: "এই স্টোরের সকল ডিল, কুপন ও অফার দেখুন।",
    },
  },
  Categories: {
    en: "All Categories",
    bn: "সকল ক্যাটাগরি",
    description: {
      en: "Find deals by your favorite shopping categories.",
      bn: "আপনার পছন্দের ক্যাটাগরি অনুযায়ী ডিল খুঁজুন।",
    },
  },
  Coupons: {
    en: "Promo Codes",
    bn: "কুপন কোড",
    description: {
      en: "Active promo codes and discount coupons from top Bangladeshi stores.",
      bn: "সেরা বাংলাদেশি স্টোরগুলোর সক্রিয় প্রোমো কোড ও ডিসকাউন্ট কুপন।",
    },
  },
  Deals: {
    en: "All Deals",
    bn: "সকল ডিল",
    description: {
      en: "Browse the latest deals and limited-time offers on DealPabo.",
      bn: "DealPabo তে সর্বশেষ ডিল ও সীমিত সময়ের অফার দেখুন।",
    },
  },
  Brands: {
    en: "All Brands",
    bn: "সকল ব্র্যান্ড",
    description: {
      en: "Discover deals and coupons from your favorite brands in one place.",
      bn: "আপনার প্রিয় ব্র্যান্ডগুলোর সেরা ডিল ও কুপন এক জায়গায় খুঁজুন।",
    },
  },
  Contact: {
    en: "Contact Us",
    bn: "যোগাযোগ",
    description: {
      en: "Get in touch with the DealPabo team — we are happy to help with questions, feedback, or partnership inquiries.",
      bn: "DealPabo টিমের সাথে যোগাযোগ করুন — আপনার প্রশ্ন, মতামত বা অংশীদারিত্বের অনুসন্ধানে আমরা সাহায্য করতে প্রস্তুত।",
    },
  },
  About: {
    en: "About Us",
    bn: "আমাদের সম্পর্কে",
    description: {
      en: "Learn more about DealPabo — Bangladesh's home for the best deals and coupons.",
      bn: "DealPabo সম্পর্কে আরও জানুন — বাংলাদেশের সেরা ডিল ও কুপনের ঠিকানা।",
    },
  },
  Terms: {
    en: "Terms & Conditions",
    bn: "শর্তাবলী",
    description: {
      en: "Read DealPabo's terms and conditions for using the website and services.",
      bn: "DealPabo ওয়েবসাইট ও সেবা ব্যবহারের শর্তাবলী পড়ুন।",
    },
  },
  Privacy: {
    en: "Privacy Policy",
    bn: "গোপনীয়তা নীতি",
    description: {
      en: "DealPabo's privacy policy — how we collect, use, and protect your data.",
      bn: "DealPabo এর গোপনীয়তা নীতি — আমরা কীভাবে আপনার তথ্য সংগ্রহ, ব্যবহার ও সুরক্ষা করি।",
    },
  },
  FAQ: {
    en: "Frequently Asked Questions",
    bn: "প্রশ্নোত্তর",
    description: {
      en: "Find answers to common questions about orders, payments, delivery, and returns on DealPabo.",
      bn: "DealPabo তে অর্ডার, পেমেন্ট, ডেলিভারি ও রিটার্ন সম্পর্কে সাধারণ প্রশ্নের উত্তর।",
    },
  },
  ReturnPolicy: {
    en: "Return Policy",
    bn: "রিটার্ন পলিসি",
    description: {
      en: "DealPabo's return and refund policy — what to do if a product is defective or you change your mind.",
      bn: "DealPabo এর রিটার্ন ও রিফান্ড নীতি — পণ্য ত্রুটিপূর্ণ হলে বা মত পরিবর্তন করলে কী করবেন।",
    },
  },
  Cart: {
    en: "Your Cart",
    bn: "আপনার কার্ট",
    description: {
      en: "Review items in your cart and proceed to checkout.",
      bn: "আপনার কার্টে থাকা পণ্যগুলো দেখুন এবং চেকআউট করুন।",
    },
  },
  Checkout: {
    en: "Checkout",
    bn: "চেকআউট",
    description: {
      en: "Complete your order by providing shipping and payment details.",
      bn: "শিপিং ও পেমেন্ট তথ্য দিয়ে আপনার অর্ডার সম্পন্ন করুন।",
    },
  },
  UserDashboard: {
    en: "My Dashboard",
    bn: "আমার ড্যাশবোর্ড",
    description: {
      en: "Manage your orders, addresses, and account settings.",
      bn: "আপনার অর্ডার, ঠিকানা ও অ্যাকাউন্ট সেটিংস পরিচালনা করুন।",
    },
  },
  AdminSliders: {
    en: "Manage Sliders",
    bn: "স্লাইডার ম্যানেজমেন্ট",
  },
  NotFound: {
    en: "Page Not Found",
    bn: "পেজ পাওয়া যায়নি",
    description: {
      en: "The page you are looking for does not exist or has been moved.",
      bn: "আপনি যে পেজটি খুঁজছেন তা নেই অথবা সরিয়ে ফেলা হয়েছে।",
    },
  },
};
