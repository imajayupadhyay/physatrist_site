<script setup>
import { computed, ref } from 'vue';
import Icon from '@/Components/Global/Icon.vue';
import { site } from '@/Config/site';

// Managed in Admin → Home page → Videos. YouTube reels play inline; Instagram reels open the post.
const props = defineProps({
    content: { type: Object, required: true },
});

const reels = computed(() =>
    props.content.reels.map((reel) => ({
        ...reel,
        youtubeId: reel.platform === 'youtube' ? reel.video : null,
        href: reel.platform === 'instagram' ? reel.video : null,
    })),
);

const playing = ref({});
const play = (reel) => (playing.value[reel.youtubeId] = true);

const embedUrl = (id) => `https://www.youtube-nocookie.com/embed/${id}?autoplay=1&rel=0&modestbranding=1`;
</script>

<template>
    <section id="videos" class="videos">
        <div class="wrap">
            <div class="vid-top">
                <div v-reveal class="head">
                    <span v-if="content.eyebrow" class="eyebrow">{{ content.eyebrow }}</span>
                    <h2>{{ content.heading }}</h2>
                    <p v-if="content.lead" class="lead">{{ content.lead }}</p>
                </div>
                <div v-reveal class="channel">
                    <img :src="content.channel_image" alt="" />
                    <div class="ch-text"><b>{{ content.channel_name }}</b><small v-if="content.channel_subtitle">{{ content.channel_subtitle }}</small></div>
                    <div class="ch-links">
                        <a v-if="site.youtube" :href="site.youtube" target="_blank" rel="noopener" class="ch-btn" aria-label="Subscribe on YouTube"><Icon name="i-yt" :size="20" /></a>
                        <a v-if="site.instagram" :href="site.instagram" target="_blank" rel="noopener" class="ch-btn" aria-label="Follow on Instagram"><Icon name="i-ig" :size="20" /></a>
                    </div>
                </div>
            </div>

            <div v-if="reels.length" class="reels">
                <article v-for="(reel, i) in reels" :key="i" v-reveal class="reel">
                    <component
                        :is="reel.youtubeId ? 'button' : 'a'"
                        class="reel-media"
                        v-bind="reel.youtubeId ? {} : { href: reel.href, target: '_blank', rel: 'noopener' }"
                        :aria-label="reel.youtubeId ? `Play video: ${reel.title}` : `Watch on Instagram: ${reel.title}`"
                        @click="reel.youtubeId && play(reel)"
                    >
                        <img :src="reel.image" alt="" loading="lazy" />
                        <span class="cover-text">{{ reel.cover }}<em v-if="reel.cover_em">{{ reel.cover_em }}</em></span>
                        <span class="platform">
                            <Icon :name="reel.youtubeId ? 'i-yt' : 'i-ig'" :size="15" />{{ reel.youtubeId ? 'YouTube' : 'Reel' }}
                        </span>
                        <span class="play"><Icon name="i-play" :size="24" /></span>
                        <span class="cover-title"><Icon name="logo" /><span><small>{{ content.channel_name }}</small>{{ content.channel_subtitle.replace(/^with\s+/i, '') }}</span></span>
                        <iframe
                            v-if="reel.youtubeId && playing[reel.youtubeId]"
                            :src="embedUrl(reel.youtubeId)"
                            title="Mind Essence video"
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowfullscreen
                        />
                    </component>
                    <div class="reel-body">
                        <small v-if="reel.topic">{{ reel.topic }}</small>
                        <h3>{{ reel.title }}</h3>
                        <button v-if="reel.youtubeId" class="watch" @click="play(reel)">Play here <Icon name="i-play" :size="14" /></button>
                        <a v-else class="watch" :href="reel.href" target="_blank" rel="noopener">Watch on Instagram <Icon name="i-arrow" :size="14" /></a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<style scoped>
.videos { background: var(--paper); }
.vid-top { display: flex; justify-content: space-between; align-items: flex-end; gap: 40px; margin-bottom: 48px; }
.vid-top .head { margin-bottom: 0; }
.channel { display: flex; align-items: center; gap: 16px; background: var(--cream); border: 1px solid var(--line); border-radius: 999px; padding: 8px 8px 8px 8px; flex: none; }
.channel img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; object-position: center 10%; background: #e8e6e2; }
.channel .ch-text { display: flex; flex-direction: column; line-height: 1.3; padding-right: 6px; }
.channel .ch-text b { font-family: var(--serif); font-weight: 600; font-size: 1.15rem; color: var(--forest-deep); }
.channel .ch-text small { font-size: 0.8rem; color: var(--muted); }
.channel .ch-links { display: flex; gap: 8px; }
.ch-btn { width: 44px; height: 44px; border-radius: 50%; display: grid; place-items: center; background: var(--forest); color: #fff; transition: all 0.3s; }
.ch-btn:hover { background: var(--gold); transform: translateY(-2px); }
.reels { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.reel { display: flex; flex-direction: column; background: var(--cream); border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; transition: transform 0.4s ease, box-shadow 0.4s ease; }
.reel:hover { transform: translateY(-6px); box-shadow: var(--shadow); }
.reel-media { position: relative; display: block; width: 100%; aspect-ratio: 4/5; background: radial-gradient(120% 80% at 100% 0%, #3d5a46 0%, var(--forest) 45%, var(--forest-deep) 100%); border: 0; padding: 0; cursor: pointer; overflow: hidden; text-align: left; color: #fff; }
.reel-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.22; filter: grayscale(1); mix-blend-mode: luminosity; transition: transform 0.8s ease; }
.reel-media .cover-text { position: absolute; z-index: 2; left: 28px; right: 28px; top: 72px; font-family: var(--serif); font-size: clamp(1.9rem, 2.6vw, 2.4rem); line-height: 1.08; color: #fff; }
.reel-media .cover-text em { display: block; font-style: italic; color: var(--gold-soft); }
.reel-media .cover-text::after { content: ''; display: block; width: 36px; height: 2px; background: var(--gold); margin-top: 18px; }
.reel:hover .reel-media img { transform: scale(1.05); }
.reel-media::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(20, 32, 25, 0) 55%, rgba(20, 32, 25, 0.6) 100%); pointer-events: none; }
.reel-media .platform { position: absolute; z-index: 2; top: 22px; left: 24px; display: inline-flex; align-items: center; gap: 7px; background: rgba(255, 253, 249, 0.94); color: var(--forest-deep); padding: 6px 12px 6px 9px; border-radius: 999px; font-size: 0.74rem; font-weight: 500; letter-spacing: 0.06em; }
.reel-media .platform svg { color: var(--forest); }
.reel-media .play { position: absolute; z-index: 2; right: 22px; bottom: 20px; width: 58px; height: 58px; border-radius: 50%; background: rgba(255, 253, 249, 0.95); display: grid; place-items: center; color: var(--forest); transition: transform 0.3s; box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5); }
.reel:hover .reel-media .play { transform: scale(1.08); }
.reel-media .cover-title { position: absolute; z-index: 2; left: 24px; right: 96px; bottom: 24px; display: flex; align-items: center; gap: 10px; }
.reel-media .cover-title svg { width: 30px; height: 30px; color: #fff; flex: none; }
.reel-media .cover-title span { font-family: var(--serif); font-size: 1.05rem; letter-spacing: 0.02em; line-height: 1.15; }
.reel-media .cover-title small { display: block; font-family: var(--sans); font-size: 0.62rem; letter-spacing: 0.26em; text-transform: uppercase; color: var(--gold-soft); }
.reel-media iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; z-index: 3; background: #000; }
.reel-body { padding: 22px 24px 24px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
.reel-body small { font-size: 0.72rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gold); font-weight: 500; }
.reel-body h3 { font-size: 1.4rem; }
.reel-body .watch { margin-top: auto; padding-top: 12px; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.05em; color: var(--forest); display: inline-flex; align-items: center; gap: 8px; align-self: flex-start; background: none; border: 0; cursor: pointer; }
.reel-body .watch:hover { color: var(--gold); }

@media (max-width: 1100px) {
    .vid-top { flex-direction: column; align-items: flex-start; gap: 28px; }
}
@media (max-width: 900px) {
    .reels { grid-auto-flow: column; grid-template-columns: none; grid-auto-columns: minmax(260px, 46%); overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 12px; margin-inline: -20px; padding-inline: 20px; scroll-padding-inline: 20px; scrollbar-width: none; }
    .reels::-webkit-scrollbar { display: none; }
    .reel { scroll-snap-align: start; }
    .reel:hover { transform: none; }
}
@media (max-width: 600px) {
    .channel { border-radius: var(--radius); width: 100%; }
    .channel .ch-text { flex: 1; }
    .reels { grid-auto-columns: 80%; margin-inline: -16px; padding-inline: 16px; scroll-padding-inline: 16px; gap: 16px; }
}
</style>
