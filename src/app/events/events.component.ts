import { Component, HostListener, OnInit } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { YouTubePlayer, YouTubePlayerModule } from '@angular/youtube-player';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            YouTubePlayer, YouTubePlayerModule,
            ScrollButtonComponent
  ],
  templateUrl: './events.component.html',
  styleUrl: './events.component.css'
})
export class EventsComponent{
  playerConfig = {
    controls: 0,
    mute: 1,
    autoplay: 0
  };
}
