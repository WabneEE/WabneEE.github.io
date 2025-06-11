import { readdirSync as e, readFileSync as i, writeFileSync as s } from "fs";
import { minify as t } from "html-minifier-terser";
import r from "clean-css";
import { minify as n } from "terser";
async function minifyFiles() {
  const o = e(".").filter((e) => e.match(/\.(html|css|js)$/i));
  for (const e of o)
    try {
      const o = i(e, "utf8");
      let m = "";
      if (e.endsWith(".html"))
        m = await t(o, {
          collapseWhitespace: !0,
          removeComments: !0,
          removeRedundantAttributes: !0,
          removeEmptyAttributes: !0,
          minifyCSS: !1,
          minifyJS: !1,
          preserveLineBreaks: !1,
          keepClosingSlash: !0,
          decodeEntities: !0,
        });
      else if (e.endsWith(".css")) m = new r({}).minify(o).styles;
      else if (e.endsWith(".js")) {
        const e = await n(o, {
          ecma: 2020,
          keep_fnames: !0,
          mangle: { toplevel: !0, keep_classnames: !0, keep_fnames: !0 },
          compress: { passes: 2, keep_infinity: !0 },
          format: { comments: !1, beautify: !1, ascii_only: !1 },
        });
        if (e.error) throw e.error;
        m = e.code;
      }
      s(e, m, "utf8"), console.log(`Minified: ${e}`);
    } catch (i) {
      console.error(`Error minifying ${e}:`, i);
    }
}
minifyFiles();
