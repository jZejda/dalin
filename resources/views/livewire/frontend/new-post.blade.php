@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <div class="screen flex flex-col">
            <div class="flex-1 flex flex-row">
                <div class="w-full">
                    <article class="format lg:format-lg">

                        <p class="font-light text-gray-500 dark:text-gray-400">Track work across the enterprise through an open, collaborative platform. <a href="#" class="font-semibold text-gray-900 underline dark:text-white decoration-indigo-500">Link issues across Jira</a> and ingest data from other <a href="#" class="font-semibold text-gray-900 underline dark:text-white decoration-blue-500">software development<a> tools, so your IT support and operations teams have richer contextual information to rapidly respond to <a href="#" class="font-semibold text-gray-900 underline dark:text-white decoration-green-500">requests</a>, <a href="#" class="font-semibold text-gray-900 underline dark:text-white decoration-red-500">incidents</a>, and <a href="#" class="font-semibold text-gray-900 underline dark:text-white decoration-sky-500">changes</a>.</p>

                        <h1>Prototyping from A to Z: best practices for successful prototypes</h1>
                        <p class="lead">Flowbite is an open-source library of UI components built with the utility-first classes from Tailwind CSS. It also includes interactive elements such as dropdowns, modals, datepickers.</p>
                        <p>Before going digital, you might benefit from scribbling down some ideas in a sketchbook. This way, you can think things through before committing to an actual design project.</p>
                        <p>But then I found a <a href="#">component library based on Tailwind CSS called Flowbite</a>. It comes with the most commonly used UI components, such as buttons, navigation bars, cards, form elements, and more which are conveniently built with the utility classes from Tailwind CSS.</p>

                        ...

                        <h2>When does design come in handy?</h2>
                        <p>While it might seem like extra work at a first glance, here are some key moments in which prototyping will come in handy:</p>
                        <ol>
                            <li><strong>Usability testing</strong>. Does your user know how to exit out of screens? Can they follow your intended user journey and buy something from the site you’ve designed? By running a usability test, you’ll be able to see how users will interact with your design once it’s live;</li>
                            <li><strong>Involving stakeholders</strong>. Need to check if your GDPR consent boxes are displaying properly? Pass your prototype to your data protection team and they can test it for real;</li>
                            <li><strong>Impressing a client</strong>. Prototypes can help explain or even sell your idea by providing your client with a hands-on experience;</li>
                            <li><strong>Communicating your vision</strong>. By using an interactive medium to preview and test design elements, designers and developers can understand each other — and the project — better.</li>
                        </ol>
                    </article>

                    <livewire:frontend.post-cards />
                </div>
            </div>
        </div>
    </div>

@endsection
