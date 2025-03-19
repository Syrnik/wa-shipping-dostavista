<script setup xmlns="http://www.w3.org/1999/html">
import {ref} from "vue";

const props = defineProps({vars: {type: Array, default: () => ['z', 'y', 's']}});

const count_txt = {1: 'доступна одна переменная', 2: 'доступны две переменные', 3: 'доступны три переменные'};

const formula_desc = ref(null);

function openDrawer() {
    $.waDrawer({
        html: [].map.call(formula_desc.value.children, e => e.outerHTML).join('\n'),
        direction: 'right'
    })
}
</script>

<template>
    <a href="#" @click.prevent="openDrawer"><i class="far fa-question-circle"></i></a>
    <template ref="formula_desc">
        <div id="" class="drawer">
            <div class="drawer-background"></div>
            <div class="drawer-body">
                <a class="drawer-close js-close-drawer" href="#"><i class="fas fa-times"></i></a>
                <div class="drawer-block w-syrnik-shipping-ui">
                    <header class="drawer-header">
                        <h1>
                            <slot name="header">Формула для расчёта наценки</slot>
                        </h1>
                    </header>
                    <div class="drawer-content">
                        <p>Для составления формулы {{ count_txt[vars.length] }}:</p>
                        <dl class="w-syrnik-shipping-terms">
                            <dt><var class="semibold">Z</var></dt>
                            <dd>стоимость заказа с учётом скидок</dd>
                            <dt><var class="semibold">Y</var></dt>
                            <dd>стоимость заказа без учёта скидок</dd>
                            <dt v-if="vars.includes('s')"><var class="semibold">S</var></dt>
                            <dd v-if="vars.includes('s')">стоимость доставки, рассчитанная сервером перевозчика</dd>
                        </dl>
                        <p>Можно использовать математические символы (сложение, деление, вычитание, умножение и т.д.),
                            скобки
                            <span class="hint">(квадратный корень, синус, косинус тоже можно)</span>.</p>
                        <p><span class="text-orange"><i class="fas fa-exclamation-triangle"></i></span> Если используете
                            дробные значения, в качестве разделителя десятичной части должна быть точка!</p>
                        <p>Примеры формул:</p>
                        <dl class="w-syrnik-shipping-terms">
                            <dt><code class="semibold highlighted green">150</code></dt>
                            <dd>Фиксированная стоимость 150₽</dd>
                            <dt><code class="semibold highlighted green">Z*0.1+100</code></dt>
                            <dd>10% от суммы заказа (Z*0.1) плюс ещё 100 рублей</dd>
                            <dt v-if="vars.includes('s')"><code class="semibold highlighted green">Z*0.025+S+100</code>
                            </dt>
                            <dd v-if="vars.includes('s')">2.5% от суммы заказа (Z*0.025) плюс расчетная стоимость
                                доставки плюс ещё 100 рублей
                            </dd>
                            <dt v-if="vars.includes('s')"><code class="semibold highlighted green">Z*0.1+S^2</code></dt>
                            <dd v-if="vars.includes('s')">10% от суммы заказа плюс квадрат стоимости доставки (доставка
                                в степени 2) 🙃
                            </dd>
                            <dt v-if="vars.includes('y')"><code class="semibold highlighted green">max(1000, Y)</code>
                            </dt>
                            <dd v-if="vars.includes('y')">стоимость заказа без учёта скидок, но не менее 1000&nbsp;₽
                            </dd>
                            <dt v-if="vars.includes('z')"><code class="semibold highlighted green">min(5000, Z)</code>
                            </dt>
                            <dd v-if="vars.includes('z')">стоимость заказа с учётом скидок, но не более 5000&nbsp;₽</dd>
                        </dl>
                    </div>
                    <footer class="drawer-footer">
                        <button class="js-close-drawer button light-gray">Закрыть</button>
                    </footer>
                </div>
            </div>
        </div>
    </template>
</template>

<style lang="stylus">
.w-syrnik-shipping-ui
    var
        font-family monospace
        font-style normal

dl.w-syrnik-shipping-terms
    display grid
    grid-auto-flow column;
    grid-gap 1.2rem 1rem

    dt
        grid-column-start 1

</style>
