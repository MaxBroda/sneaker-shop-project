// @ts-check
import withNuxt from "./.nuxt/eslint.config.mjs";

export default withNuxt({
  files: ["**/*.vue"],
  rules: {
    "vue/multi-word-component-names": "off",
    "vue/attributes-order": [
      "warn",
      {
        order: [
          "DEFINITION",
          "LIST_RENDERING",
          "CONDITIONALS",
          "RENDER_MODIFIERS",
          "GLOBAL",
          ["UNIQUE", "SLOT"],
          "TWO_WAY_BINDING",
          "OTHER_DIRECTIVES",
          "OTHER_ATTR",
          "ATTR_STATIC",
          "ATTR_DYNAMIC",
          "ATTR_SHORTHAND_BOOL",
          "EVENTS",
          "CONTENT",
        ],
        alphabetical: false,
      },
    ],
  },
});
